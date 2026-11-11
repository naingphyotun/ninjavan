<?php
header('Content-Type: application/json');
require_once __DIR__ . "/dbinfo.php";

$origin_site_id = isset($_GET['origin_site_id']) ? (int)$_GET['origin_site_id'] : 0;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;   // default 1
$weight_each = isset($_GET['weight']) ? (int)$_GET['weight'] : 0;
$size_each = isset($_GET['size']) ? (int)$_GET['size'] : 0;
$is_hazardous = isset($_GET['is_hazardous']) ? (int)$_GET['is_hazardous'] : 0;

if ($origin_site_id <= 0 || $quantity <= 0 || $weight_each <= 0 || $size_each <= 0) {
  echo json_encode(["success"=>false, "message"=>"Missing or invalid parameters."]);
  exit;
}

$total_weight = $quantity * $weight_each;
$total_size   = $quantity * $size_each;

$sql = "SELECT
          inventory_id,
          vehicle_type,
          max_weight,
          max_space,
          hazmat_capable,
          image_url,
          total_count,
          available_count
        FROM site_vehicle_inventory
        WHERE site_id = ?
          AND available_count > 0
          AND max_weight >= ?
          AND max_space >= ?
          AND (? = 0 OR hazmat_capable = 1)
        ORDER BY
          CASE vehicle_type
            WHEN 'Bicycle' THEN 1
            WHEN 'Bike' THEN 2
            WHEN 'Van' THEN 3
            WHEN 'Truck' THEN 4
            ELSE 99
          END";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $origin_site_id, $total_weight, $total_size, $is_hazardous);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode(["success"=>true, "data"=>$data]);
