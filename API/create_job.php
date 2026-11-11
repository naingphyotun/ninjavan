<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . "/dbinfo.php";

if (!isset($_SESSION['user_email'])) {
  echo json_encode(["success"=>false, "message"=>"Not logged in."]);
  exit;
}
$role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
$role = strtolower($role);

// 1. Determine Initial Status
// Staff -> Outstanding (Approved immediately)
// Customer -> Pending (Needs approval)
$initialStatus = ($role === 'staff') ? 'Outstanding' : 'Pending';

$user_email = $_SESSION['user_email'];
$job_name = trim($_POST['job_name'] ?? '');
$quantity = (int)($_POST['quantity'] ?? 0);
$weight_each = (int)($_POST['weight'] ?? 0);
$size_each = (int)($_POST['size'] ?? 0);
$is_hazardous = (int)($_POST['is_hazardous'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$deadline = $_POST['deadline'] ?? '';
$origin_site_id = (int)($_POST['origin_site_id'] ?? 0);
$destination_site_id = (int)($_POST['destination_site_id'] ?? 0);
$inventory_id = (int)($_POST['vehicle_id'] ?? 0); 
$vehicle_type = trim($_POST['vehicle_type'] ?? '');

if ($job_name === '' || $quantity <= 0 || $weight_each <= 0 || $size_each <= 0 ||
    $origin_site_id <= 0 || $destination_site_id <= 0 || $inventory_id <= 0 ||
    $start_date === '' || $deadline === '') {
  echo json_encode(["success"=>false, "message"=>"Please fill all fields correctly."]);
  exit;
}

if ($origin_site_id === $destination_site_id) {
  echo json_encode(["success"=>false, "message"=>"Origin and destination must be different."]);
  exit;
}

if ($start_date > $deadline) {
  echo json_encode(["success"=>false, "message"=>"Start date must be on or before deadline."]);
  exit;
}

try {
  // Check if table has status column
  $colsRes = $conn->query("SHOW COLUMNS FROM jobs");
  $jobCols = [];
  while ($r = $colsRes->fetch_assoc()) { $jobCols[] = $r['Field']; }
  $hasStatus = in_array("status", $jobCols);

  if ($hasStatus) {
    // FIX: Used $initialStatus instead of hardcoded 'Pending'
    $ins = $conn->prepare("
      INSERT INTO jobs
        (job_name, quantity, weight, size, is_hazardous, start_date, deadline,
         origin_site_id, destination_site_id, vehicle_id, vehicle_type, user_email, status)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->bind_param(
      "siiiissiiisss",
      $job_name, $quantity, $weight_each, $size_each, $is_hazardous,
      $start_date, $deadline,
      $origin_site_id, $destination_site_id,
      $inventory_id, $vehicle_type, $user_email, $initialStatus
    );
  } else {
    // Fallback for old schema
    $ins = $conn->prepare("
      INSERT INTO jobs
        (job_name, quantity, weight, size, is_hazardous, start_date, deadline,
         origin_site_id, destination_site_id, vehicle_id, vehicle_type, user_email)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->bind_param(
      "siiiissiiiss",
      $job_name, $quantity, $weight_each, $size_each, $is_hazardous,
      $start_date, $deadline,
      $origin_site_id, $destination_site_id,
      $inventory_id, $vehicle_type, $user_email
    );
  }

  if (!$ins->execute()) {
    throw new Exception($ins->error);
  }
  
  $job_id = $conn->insert_id;

  // IMPORTANT: If Staff created it (Outstanding), we must DECREASE inventory immediately
  if ($initialStatus === 'Outstanding') {
     $upd = $conn->query("UPDATE site_vehicle_inventory SET available_count = available_count - 1 WHERE inventory_id = $inventory_id AND available_count > 0");
  }

  echo json_encode(["success"=>true, "job_id"=>$job_id, "status"=>$initialStatus]);

} catch (Throwable $e) {
  echo json_encode(["success"=>false, "message"=>"Server error: " . $e->getMessage()]);
}
?>