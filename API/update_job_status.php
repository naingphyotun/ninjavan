<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/dbinfo.php';

try {
    // Only staff can update job status
    $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
    if ($role !== 'staff') {
        echo json_encode([ "success" => false, "message" => "Forbidden. Staff only." ]);
        exit;
    }
    // Ensure DB connection
    if (!isset($conn) || !$conn || ($conn instanceof mysqli && $conn->connect_errno)) {
        throw new Exception("DB connection not available.");
    }

    if (!isset($_POST['job_id'])) {
        echo json_encode([ "success" => false, "message" => "Missing job_id" ]);
        exit;
    }
    $jobId = intval($_POST['job_id']);
    if ($jobId <= 0) {
        echo json_encode([ "success" => false, "message" => "Invalid job_id" ]);
        exit;
    }

    // Supported workflow:
    // Customer creates job as: Pending
    // Staff approves: Pending -> Outstanding  (and reserves a vehicle)
    // Staff completes: Outstanding -> Completed (and moves vehicle to destination)
    //
    // This endpoint supports:
    // - action=approve
    // - action=complete
    // Backward compatible:
    // - status=outstanding/completed (will attempt to set directly if valid)
    $action = strtolower(trim($_POST['action'] ?? ''));
    $requestedStatus = strtolower(trim($_POST['status'] ?? ''));

    // Fetch job details we need
    $stmt = $conn->prepare("
        SELECT job_id, status, origin_site_id, destination_site_id,
               vehicle_id, vehicle_type,
               quantity, weight, size, is_hazardous
        FROM jobs
        WHERE job_id = ?
        LIMIT 1
    ");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    $res = $stmt->get_result();
    $job = $res ? $res->fetch_assoc() : null;
    if (!$job) {
        echo json_encode([ "success" => false, "message" => "Job not found." ]);
        exit;
    }

    $current = strtolower(trim($job['status'] ?? 'outstanding'));
    $originSite = intval($job['origin_site_id'] ?? 0);
    $destSite   = intval($job['destination_site_id'] ?? 0);
    $inventoryId = intval($job['vehicle_id'] ?? 0); // site_vehicle_inventory.inventory_id
    $vehicleType = trim($job['vehicle_type'] ?? '');

    $qty = intval($job['quantity'] ?? 1);
    $weightEach = intval($job['weight'] ?? 0);
    $sizeEach   = intval($job['size'] ?? 0);
    $isHaz = intval($job['is_hazardous'] ?? 0);

    $totalWeight = $qty * $weightEach;
    $totalSize   = $qty * $sizeEach;

    // Determine requested transition
    $target = null;
    if ($action === 'approve') $target = 'outstanding';
    if ($action === 'complete') $target = 'completed';

    if (!$target && $requestedStatus) {
        if (in_array($requestedStatus, ['pending','outstanding','completed'], true)) {
            $target = $requestedStatus;
        } else {
            echo json_encode([ "success" => false, "message" => "Invalid status value" ]);
            exit;
        }
    }

    if (!$target) {
        // Fallback toggle: pending->outstanding->completed (no reverse)
        if ($current === 'pending') $target = 'outstanding';
        elseif ($current === 'outstanding') $target = 'completed';
        else $target = 'completed';
    }

    // Enforce workflow rules
    if ($target === 'outstanding' && $current !== 'pending') {
        echo json_encode([ "success" => false, "message" => "Only Pending jobs can be approved." ]);
        exit;
    }
    if ($target === 'completed' && $current !== 'outstanding') {
        echo json_encode([ "success" => false, "message" => "Only Outstanding jobs can be completed." ]);
        exit;
    }
    if ($target === 'pending') {
        echo json_encode([ "success" => false, "message" => "Cannot move job back to Pending." ]);
        exit;
    }

    $conn->begin_transaction();

    // If approving: reserve vehicle now (so it disappears from origin availability)
    if ($target === 'outstanding') {
        if ($inventoryId <= 0 || $originSite <= 0) {
            $conn->rollback();
            echo json_encode([ "success" => false, "message" => "Job has no vehicle/origin assigned." ]);
            exit;
        }

        $reserve = $conn->prepare("
            UPDATE site_vehicle_inventory
            SET available_count = available_count - 1
            WHERE inventory_id = ?
              AND site_id = ?
              AND available_count > 0
              AND max_weight >= ?
              AND max_space >= ?
              AND (? = 0 OR hazmat_capable = 1)
        ");
        if (!$reserve) throw new Exception("Prepare failed: " . $conn->error);
        $reserve->bind_param("iiiii", $inventoryId, $originSite, $totalWeight, $totalSize, $isHaz);
        $reserve->execute();

        if ($reserve->affected_rows !== 1) {
            $conn->rollback();
            echo json_encode([ "success" => false, "message" => "Cannot approve: selected vehicle is not available/suitable now." ]);
            exit;
        }
    }

    // If completing: move vehicle from origin to destination
    if ($target === 'completed') {
        if ($inventoryId <= 0 || $originSite <= 0 || $destSite <= 0) {
            $conn->rollback();
            echo json_encode([ "success" => false, "message" => "Job is missing vehicle/origin/destination." ]);
            exit;
        }

        // Read origin inventory row (to copy specs)
        $q = $conn->prepare("SELECT vehicle_type, max_weight, max_space, hazmat_capable, image_url, total_count FROM site_vehicle_inventory WHERE inventory_id = ? LIMIT 1");
        if (!$q) throw new Exception("Prepare failed: " . $conn->error);
        $q->bind_param("i", $inventoryId);
        $q->execute();
        $r = $q->get_result();
        $inv = $r ? $r->fetch_assoc() : null;
        if (!$inv) {
            $conn->rollback();
            echo json_encode([ "success" => false, "message" => "Inventory record not found for this job." ]);
            exit;
        }

        $vType = trim($inv['vehicle_type'] ?? $vehicleType);
        if ($vType === '') $vType = $vehicleType;

        // 1) Decrease origin total_count by 1 (vehicle left the origin site)
        $dec = $conn->prepare("
            UPDATE site_vehicle_inventory
            SET total_count = total_count - 1
            WHERE inventory_id = ?
              AND total_count > 0
        ");
        if (!$dec) throw new Exception("Prepare failed: " . $conn->error);
        $dec->bind_param("i", $inventoryId);
        $dec->execute();
        if ($dec->affected_rows !== 1) {
            $conn->rollback();
            echo json_encode([ "success" => false, "message" => "Cannot complete: origin inventory total_count is invalid." ]);
            exit;
        }

        // 2) Add/Upsert destination inventory row (+1 total, +1 available)
        $findDest = $conn->prepare("SELECT inventory_id FROM site_vehicle_inventory WHERE site_id = ? AND vehicle_type = ? LIMIT 1");
        if (!$findDest) throw new Exception("Prepare failed: " . $conn->error);
        $findDest->bind_param("is", $destSite, $vType);
        $findDest->execute();
        $destRes = $findDest->get_result();
        $destRow = $destRes ? $destRes->fetch_assoc() : null;

        if ($destRow && intval($destRow['inventory_id']) > 0) {
            $destInvId = intval($destRow['inventory_id']);
            $inc = $conn->prepare("
                UPDATE site_vehicle_inventory
                SET total_count = total_count + 1,
                    available_count = available_count + 1
                WHERE inventory_id = ?
            ");
            if (!$inc) throw new Exception("Prepare failed: " . $conn->error);
            $inc->bind_param("i", $destInvId);
            $inc->execute();
        } else {
            $maxW = intval($inv['max_weight'] ?? 0);
            $maxS = intval($inv['max_space'] ?? 0);
            $haz  = intval($inv['hazmat_capable'] ?? 0);
            $img  = $inv['image_url'] ?? null;

            $ins = $conn->prepare("
                INSERT INTO site_vehicle_inventory
                    (site_id, vehicle_type, available_count, total_count, max_weight, max_space, hazmat_capable, image_url)
                VALUES (?, ?, 1, 1, ?, ?, ?, ?)
            ");
            if (!$ins) throw new Exception("Prepare failed: " . $conn->error);
            $ins->bind_param("isiiis", $destSite, $vType, $maxW, $maxS, $haz, $img);
            $ins->execute();
        }
    }

    // Update job status
    $statusForDB = ($target === 'completed') ? 'Completed' : 'Outstanding';
    if ($target === 'outstanding') $statusForDB = 'Outstanding';

    $up = $conn->prepare("UPDATE jobs SET status = ? WHERE job_id = ?");
    if (!$up) throw new Exception("Prepare failed: " . $conn->error);
    $up->bind_param("si", $statusForDB, $jobId);
    $up->execute();

    $conn->commit();

    echo json_encode([
        "success" => true,
        "job_id"  => $jobId,
        "from"    => ucfirst($current),
        "status"  => $statusForDB
    ]);
} catch (Throwable $e) {
    if (isset($conn) && $conn && method_exists($conn, 'rollback')) {
        @$conn->rollback();
    }
    echo json_encode([
        "success" => false,
        "message" => "Server error.",
        "error"   => $e->getMessage()
    ]);
}
