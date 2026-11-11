<?php
header('Content-Type: text/plain'); 
require_once 'dbinfo.php';

// Configuration
$numberOfJobsToGenerate = 50;
$fakerAdjectives = ['Urgent', 'Fragile', 'Heavy', 'Standard', 'Express', 'Bulk', 'Secure', 'International'];
$fakerGoods = ['Electronics', 'Furniture', 'Medical Supplies', 'Auto Parts', 'Textiles', 'Documents', 'Building Materials', 'Perishables'];

echo "--- STARTING DATA GENERATION (Task D) ---\n";

// 1. FETCH EXISTING SITES
echo "[1/4] Fetching your existing sites...\n";
$siteIds = [];
$res = $conn->query("SELECT site_id, site_name FROM sites");

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $siteIds[] = $row['site_id'];
    }
    echo " - Found " . count($siteIds) . " sites.\n";
} else {
    die("Error: No sites found. Please create sites in your database first.\n");
}

// 2. ENSURE VEHICLE INVENTORY EXISTS
echo "[2/4] Checking Vehicle Inventory...\n";
$vehicleTypes = [
    ['Van', 1000, 10, '../img/van.jfif'], 
    ['Truck', 5000, 50, '../img/truck.webp'],
    ['Bike', 50, 2, '../img/bike.webp']
];

foreach ($siteIds as $sid) {
    foreach ($vehicleTypes as $vt) {
        $type = $vt[0];
        $check = $conn->query("SELECT inventory_id FROM site_vehicle_inventory WHERE site_id = $sid AND vehicle_type = '$type'");
        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO site_vehicle_inventory (site_id, vehicle_type, total_count, available_count, max_weight, max_space, hazmat_capable, image_url) VALUES (?, ?, 10, 10, ?, ?, 1, ?)");
            $stmt->bind_param("isssis", $sid, $type, $vt[1], $vt[2], $vt[3]);
            $stmt->execute();
            echo " - Added $type fleet to Site ID $sid\n";
        }
    }
}

// 3. GENERATE RANDOM JOBS

echo "[3/4] Generating $numberOfJobsToGenerate Sample Jobs...\n";

// Get all inventory IDs to assign jobs to real vehicles
$inventoryIds = [];
$res = $conn->query("SELECT inventory_id, site_id, vehicle_type FROM site_vehicle_inventory");
while ($row = $res->fetch_assoc()) $inventoryIds[] = $row;

if (empty($inventoryIds)) die("Error: No vehicle inventory found.\n");

$countCreated = 0;

for ($i = 0; $i < $numberOfJobsToGenerate; $i++) {
    // Random Job Details
    $jobName = $fakerAdjectives[array_rand($fakerAdjectives)] . " " . $fakerGoods[array_rand($fakerGoods)];
    $quantity = rand(1, 5);
    $weight = rand(10, 500);
    $size = rand(1, 20);
    $isHaz = (rand(0, 10) > 8) ? 1 : 0; 
    
    // Dates
    $startTimestamp = strtotime(rand(-30, 30) . " days");
    $deadlineTimestamp = strtotime("+".rand(1, 10)." days", $startTimestamp);
    $startDate = date("Y-m-d", $startTimestamp);
    $deadline = date("Y-m-d", $deadlineTimestamp);
    
    // Status Logic: If deadline is past, random chance of being Completed
    $status = 'Outstanding';
    if ($deadlineTimestamp < time()) {
        $status = (rand(0, 1) == 1) ? 'Completed' : 'Outstanding';
    }

    // Assign Vehicle
    $inv = $inventoryIds[array_rand($inventoryIds)];
    $originId = $inv['site_id'];
    $vehicleId = $inv['inventory_id'];
    $vehicleType = $inv['vehicle_type'];
    
    // Pick Destination
    do {
        $destId = $siteIds[array_rand($siteIds)];
    } while ($destId == $originId);

    $userEmail = 'user@gmail.com'; 

    // Insert Job
    $stmt = $conn->prepare("INSERT INTO jobs (job_name, quantity, weight, size, is_hazardous, start_date, deadline, origin_site_id, destination_site_id, vehicle_id, vehicle_type, user_email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("iiiiisssiisss", 
        $jobName, $quantity, $weight, $size, $isHaz, 
        $startDate, $deadline, 
        $originId, $destId, 
        $vehicleId, $vehicleType, 
        $userEmail, $status
    );
    
    if ($stmt->execute()) {
        $countCreated++;
    }
}
echo " - Generated $countCreated jobs.\n";

// 4. CRITICAL FIX: RE-SYNC INVENTORY COUNTS

echo "[4/4] Syncing Vehicle Availability...\n";

// A. Reset all available counts to equal total counts (Clean Slate)
$conn->query("UPDATE site_vehicle_inventory SET available_count = total_count");

// B. Count how many 'Outstanding' jobs exist for each vehicle
$sqlCount = "SELECT vehicle_id, COUNT(*) as busy_count 
             FROM jobs 
             WHERE status = 'Outstanding' 
             GROUP BY vehicle_id";
$result = $conn->query($sqlCount);

// C. Subtract busy vehicles from available count
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vid = $row['vehicle_id'];
        $busy = $row['busy_count'];
        
        // Update the inventory
        $conn->query("UPDATE site_vehicle_inventory 
                      SET available_count = available_count - $busy 
                      WHERE inventory_id = $vid");
    }
}

// D. Safety check: Ensure no negative numbers
$conn->query("UPDATE site_vehicle_inventory SET available_count = 0 WHERE available_count < 0");

echo "\n--- SUCCESS! ---\n";
echo "1. Generated sample jobs.\n";
echo "2. Recalculated availability based on all 'Outstanding' jobs.\n";
echo "3. Your Vehicle Site page should now show correct numbers.\n";
?>