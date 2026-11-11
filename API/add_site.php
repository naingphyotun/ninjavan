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

  $site_name = trim($_POST['site_name'] ?? '');
  $image_url = trim($_POST['image_url'] ?? '');

  if ($site_name === '') {
    echo json_encode([ "success" => false, "message" => "Site name is required." ]);
    exit;
  }

  // Detect image_url column existence
  $colsRes = $conn->query("SHOW COLUMNS FROM sites");
  $cols = [];
  while ($r = $colsRes->fetch_assoc()) { $cols[] = $r['Field']; }
  $hasImage = in_array("image_url", $cols);

  if ($hasImage) {
    $stmt = $conn->prepare("INSERT INTO sites (site_name, image_url) VALUES (?, ?)");
    $stmt->bind_param("ss", $site_name, $image_url);
  } else {
    $stmt = $conn->prepare("INSERT INTO sites (site_name) VALUES (?)");
    $stmt->bind_param("s", $site_name);
  }

  if (!$stmt->execute()) {
    throw new Exception("Insert failed: " . $conn->error);
  }

  echo json_encode([ "success" => true, "site_id" => $conn->insert_id ]);
} catch (Throwable $e) {
  echo json_encode([ "success" => false, "message" => "Server error.", "error" => $e->getMessage() ]);
}
