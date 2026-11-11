<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/dbinfo.php';

try {
    // Ensure DB connection is available
    if (!isset($conn) || !$conn || ($conn instanceof mysqli && $conn->connect_errno)) {
        throw new Exception("DB connection not available." . ($conn->connect_error ?? ""));
    }

    $role  = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
    $email = $_SESSION['user_email'] ?? $_SESSION['email'] ?? null;
    // For non-staff, must have an email to filter jobs
    if ($role !== 'staff' && !$email) {
        echo json_encode([ "success" => false, "message" => "Missing user_email for customer view." ]);
        exit;
    }

    // Optional filter by specific Job ID
    $filterJobId = (isset($_GET['job_id']) && $_GET['job_id'] !== '') ? intval($_GET['job_id']) : null;

    // Dynamically detect columns in jobs table (to avoid unknown column errors)
    $colsRes = $conn->query("SHOW COLUMNS FROM jobs");
    $jobCols = [];
    while ($r = $colsRes->fetch_assoc()) {
        $jobCols[] = $r['Field'];
    }
    $has = function($col) use ($jobCols) { return in_array($col, $jobCols); };

    // Build SELECT clause based on existing columns
    $select = [
        "j.job_id",
        $has("job_name")        ? "j.job_name"        : "NULL AS job_name",
        $has("quantity")        ? "j.quantity"        : "NULL AS quantity",
        "j.weight",
        "j.size",
        $has("is_hazardous")    ? "j.is_hazardous"    : "0 AS is_hazardous",
        $has("start_date")      ? "j.start_date"      : "NULL AS start_date",
        $has("deadline")        ? "j.deadline"        : "NULL AS deadline",
        $has("status")          ? "j.status"          : "'outstanding' AS status",
        $has("origin_site_id")  ? "j.origin_site_id"  : "NULL AS origin_site_id",
        $has("destination_site_id") ? "j.destination_site_id" : "NULL AS destination_site_id",
        $has("user_email")      ? "j.user_email"      : "NULL AS user_email",
        $has("vehicle_type")    ? "j.vehicle_type"    : "NULL AS vehicle_type",
        $has("vehicle_id")      ? "j.vehicle_id"      : "NULL AS vehicle_id",
        $has("vehicle_image")   ? "j.vehicle_image"   : "NULL AS vehicle_image"
    ];
    // Include site names if site IDs exist, or leave out if not
    if ($has("origin_site_id")) {
        $select[] = "os.site_name AS origin_site";
    }
    if ($has("destination_site_id")) {
        $select[] = "ds.site_name AS destination_site";
    }
    // If vehicle_type column is missing but vehicle_id exists, join vehicles table to get type
    $joinVehicle = (!$has("vehicle_type") && $has("vehicle_id"));

    $sql = "SELECT " . implode(", ", $select) . " FROM jobs j ";
    if ($has("origin_site_id")) {
        $sql .= "LEFT JOIN sites os ON os.site_id = j.origin_site_id ";
    }
    if ($has("destination_site_id")) {
        $sql .= "LEFT JOIN sites ds ON ds.site_id = j.destination_site_id ";
    }
    if ($joinVehicle) {
        $sql .= "LEFT JOIN vehicles v ON v.vehicle_id = j.vehicle_id ";
        // Replace placeholder vehicle_type with actual value from vehicles table
        $sql = str_replace("NULL AS vehicle_type", "COALESCE(v.vehicle_type, NULL) AS vehicle_type", $sql);
    }

    // Build WHERE clause
    $where = [];
    $params = [];
    $types = "";
    if ($role !== 'staff') {
        // Customers see only their own jobs
        if ($has("user_email")) {
            $where[] = "j.user_email = ?";
            $params[] = $email;
            $types   .= "s";
        } else {
            echo json_encode([
                "success" => false,
                "message" => "jobs table has no user_email column, cannot filter customer jobs."
            ]);
            exit;
        }
    }
    if ($filterJobId !== null && $filterJobId > 0) {
        $where[] = "j.job_id = ?";
        $params[] = $filterJobId;
        $types   .= "i";
    }
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    // Sort by Job ID descending (newest first)
    $sql .= " ORDER BY j.job_id DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Query execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $jobs = [];
    while ($row = $result->fetch_assoc()) {
        // Add 'best_for' description for vehicle usage guidance
        $row['best_for'] = bestForVehicle($row['vehicle_type'] ?? null);
        $jobs[] = $row;
    }
    echo json_encode([
        "success" => true,
        "view"   => ($role === 'staff' ? "staff" : "customer"),
        "count"  => count($jobs),
        "jobs"   => $jobs
    ]);
} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error.",
        "error"   => $e->getMessage()
    ]);
}

// Helper to describe ideal use-case for a vehicle type
function bestForVehicle($type) {
    $t = strtolower((string)$type);
    if ($t === 'truck')   return "Large/heavy cargo, bulk shipments";
    if ($t === 'van')     return "Medium parcels, multi-drop deliveries";
    if ($t === 'bike')    return "Small parcels, fast city delivery";
    if ($t === 'bicycle') return "Light items, short-distance drops";
    return "—";
}
