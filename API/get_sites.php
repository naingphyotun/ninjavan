<?php
header('Content-Type: application/json');
require_once "dbinfo.php";

$sql = "SELECT site_id, site_name FROM sites ORDER BY site_name ASC";
$result = $conn->query($sql);

$data = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
}

echo json_encode([
  "success" => true,
  "data" => $data
]);
