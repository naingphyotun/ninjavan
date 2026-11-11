<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/dbinfo.php';

try {
  $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
  if ($role !== 'staff') {
    echo json_encode([ "success" => false, "message" => "Forbidden. Staff only." ]);
    exit;
  }
  if (!isset($conn) || !$conn || ($conn instanceof mysqli && $conn->connect_errno)) {
    throw new Exception("DB connection not available.");
  }

  $inventory_id    = isset($_POST['inventory_id']) && $_POST['inventory_id'] !== '' ? intval($_POST['inventory_id']) : null;
  $site_id         = intval($_POST['site_id'] ?? 0);
  $vehicle_type    = trim($_POST['vehicle_type'] ?? '');
  $available_count = intval($_POST['available_count'] ?? 0);
  $total_count     = intval($_POST['total_count'] ?? 0);
  $max_weight      = intval($_POST['max_weight'] ?? 0);
  $max_space       = intval($_POST['max_space'] ?? 0);
  $hazmat_capable  = intval($_POST['hazmat_capable'] ?? 0);
  $image_url       = trim($_POST['image_url'] ?? '');

  if ($site_id <= 0 || $vehicle_type === '') {
    echo json_encode([ "success" => false, "message" => "Site and vehicle type are required." ]);
    exit;
  }
  if ($available_count < 0 || $total_count < 0) {
    echo json_encode([ "success" => false, "message" => "Counts must be 0 or greater." ]);
    exit;
  }
  if ($available_count > $total_count) {
    echo json_encode([ "success" => false, "message" => "Available Count cannot exceed Total Count." ]);
    exit;
  }

  if ($inventory_id) {
    $stmt = $conn->prepare("
      UPDATE site_vehicle_inventory
      SET site_id = ?, vehicle_type = ?, available_count = ?, total_count = ?,
          max_weight = ?, max_space = ?, hazmat_capable = ?, image_url = ?
      WHERE inventory_id = ?
    ");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
    $stmt->bind_param("isiiiiisi", $site_id, $vehicle_type, $available_count, $total_count, $max_weight, $max_space, $hazmat_capable, $image_url, $inventory_id);
    if (!$stmt->execute()) throw new Exception("Update failed: " . $conn->error);
    echo json_encode([ "success" => true, "inventory_id" => $inventory_id ]);
    exit;
  }

  // Insert new row
  $stmt = $conn->prepare("
    INSERT INTO site_vehicle_inventory
      (site_id, vehicle_type, available_count, total_count, max_weight, max_space, hazmat_capable, image_url)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  ");
  if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
  $stmt->bind_param("isiiiiis", $site_id, $vehicle_type, $available_count, $total_count, $max_weight, $max_space, $hazmat_capable, $image_url);
  if (!$stmt->execute()) throw new Exception("Insert failed: " . $conn->error);

  echo json_encode([ "success" => true, "inventory_id" => $conn->insert_id ]);
} catch (Throwable $e) {
  echo json_encode([ "success" => false, "message" => "Server error.", "error" => $e->getMessage() ]);
}
