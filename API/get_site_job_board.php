<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/dbinfo.php';

try {
    // Only staff can use this API
    $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
    if ($role !== 'staff') {
        echo json_encode([ "success" => false, "message" => "Forbidden. Staff only." ]);
        exit;
    }
    // Ensure DB connection
    if (!isset($conn) || !$conn || ($conn instanceof mysqli && $conn->connect_errno)) {
        throw new Exception("DB connection not available.");
    }

    $filterJobId = (isset($_GET['job_id']) && $_GET['job_id'] !== '') ? intval($_GET['job_id']) : null;

    // Fetch all sites (to group jobs by origin site)
    $sitesRes = $conn->query("SELECT site_id, site_name FROM sites ORDER BY site_name ASC");
    $sites = [];
    if ($sitesRes) {
        while ($s = $sitesRes->fetch_assoc()) {
            $sites[] = [
                "site_id"   => intval($s["site_id"]),
                "site_name" => $s["site_name"],
                "jobs"      => [ "pending" => [], "outstanding" => [], "completed" => [], "late" => [] ],
                "counts"    => [ "pending" => 0, "outstanding" => 0, "completed" => 0, "late" => 0 ]
            ];
        }
    }

    // Build job query dynamically for optional columns
    $colsRes = $conn->query("SHOW COLUMNS FROM jobs");
    $jobCols = [];
    while ($col = $colsRes->fetch_assoc()) {
        $jobCols[] = $col['Field'];
    }
    $hasCol = function($col) use ($jobCols) { return in_array($col, $jobCols); };

    $selectFields = [
        "j.job_id",
        "j.weight",
        "j.size",
        $hasCol("is_hazardous") ? "j.is_hazardous" : "0 AS is_hazardous",
        $hasCol("start_date")   ? "j.start_date"   : "NULL AS start_date",
        $hasCol("deadline")     ? "j.deadline"     : "NULL AS deadline",
        $hasCol("status")       ? "j.status"       : "'outstanding' AS status",
        $hasCol("origin_site_id") ? "j.origin_site_id" : "0 AS origin_site_id",
        $hasCol("destination_site_id") ? "j.destination_site_id" : "0 AS destination_site_id",
        $hasCol("job_name")   ? "IFNULL(j.job_name, '') AS job_name"   : "'' AS job_name",
        $hasCol("quantity")   ? "IFNULL(j.quantity, 1) AS quantity"   : "1 AS quantity",
        $hasCol("vehicle_type") ? "IFNULL(j.vehicle_type, '') AS vehicle_type" : "'' AS vehicle_type"
        // (We ignore vehicle_id and vehicle_image here since not needed for grouping logic)
    ];
    // If vehicle_type is missing but vehicle_id exists, join vehicles to get type
    $joinVehicles = (!$hasCol("vehicle_type") && $hasCol("vehicle_id"));

    $sql = "SELECT " . implode(", ", $selectFields) . " FROM jobs j ";
    if ($hasCol("origin_site_id")) {
        $sql .= "LEFT JOIN sites os ON os.site_id = j.origin_site_id ";
    }
    if ($hasCol("destination_site_id")) {
        $sql .= "LEFT JOIN sites ds ON ds.site_id = j.destination_site_id ";
    }
    if ($joinVehicles) {
        $sql .= "LEFT JOIN vehicles v ON v.vehicle_id = j.vehicle_id ";
        $sql = str_replace("'' AS vehicle_type", "COALESCE(v.vehicle_type, '') AS vehicle_type", $sql);
    }

    // Optional filter by Job ID
    $where = [];
    $types = "";
    $params = [];
    if ($filterJobId !== null && $filterJobId > 0) {
        $where[] = "j.job_id = ?";
        $types  .= "i";
        $params[] = $filterJobId;
    }
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY j.job_id DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }
    $res = $stmt->get_result();
    if ($res === false) {
        throw new Exception("Query failed: " . $conn->error);
    }

    // Group jobs by origin site
    $byId = [];
    foreach ($sites as $idx => $site) {
        $byId[$site["site_id"]] = $idx;
    }
    $today = date("Y-m-d");
    while ($row = $res->fetch_assoc()) {
        $originId = intval($row["origin_site_id"] ?? 0);
        if (!isset($byId[$originId])) {
            // Job has an origin site not in the sites list (or originId=0 when no site linking)
            continue;
        }
        $status   = strtolower($row["status"] ?? "outstanding");
        $deadline = $row["deadline"] ?? null;
        $isLate   = ($status === "outstanding" && $deadline && $deadline < $today);

        // Add descriptive 'best_for' field for vehicle usage
        $row["best_for"] = bestForVehicle($row["vehicle_type"] ?? null);

                if ($status === "pending") {
            $sites[$byId[$originId]]["jobs"]["pending"][]      = $row;
            $sites[$byId[$originId]]["counts"]["pending"]++;
        } elseif ($isLate) {
            $sites[$byId[$originId]]["jobs"]["late"][]       = $row;
            $sites[$byId[$originId]]["counts"]["late"]++;
        } elseif ($status === "completed") {
            $sites[$byId[$originId]]["jobs"]["completed"][]  = $row;
            $sites[$byId[$originId]]["counts"]["completed"]++;
        } else {
            $sites[$byId[$originId]]["jobs"]["outstanding"][] = $row;
            $sites[$byId[$originId]]["counts"]["outstanding"]++;
        }
    }
    echo json_encode([ "success" => true, "sites" => $sites ]);
} catch (Throwable $e) {
    echo json_encode([ "success" => false, "message" => "Server error.", "error" => $e->getMessage() ]);
}

function bestForVehicle($type) {
    $t = strtolower((string)$type);
    if ($t === 'truck')   return "Large/heavy cargo, bulk shipments";
    if ($t === 'van')     return "Medium parcels, multi-drop deliveries";
    if ($t === 'bike')    return "Small parcels, fast city delivery";
    if ($t === 'bicycle') return "Light items, short-distance drops";
    return "—";
}
