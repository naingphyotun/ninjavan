<?php
// API/reset_data.php
header('Content-Type: text/plain'); 
require_once 'dbinfo.php';

echo "--- RESETTING SYSTEM DATA ---\n";

// 1. DELETE ALL JOBS
echo "[1/2] Deleting all jobs...\n";
if ($conn->query("DELETE FROM jobs")) {
    // Also reset the Auto-Increment so new jobs start at ID 1 again
    $conn->query("ALTER TABLE jobs AUTO_INCREMENT = 1");
    echo " - Successfully cleared the jobs table.\n";
} else {
    echo " - Error clearing jobs: " . $conn->error . "\n";
}

// 2. RESET VEHICLE AVAILABILITY
echo "[2/2] Resetting vehicle inventory counts...\n";
// This sets available_count back to the total_count for every vehicle at every site
$sqlResetInv = "UPDATE site_vehicle_inventory SET available_count = total_count";
if ($conn->query($sqlResetInv)) {
    echo " - All vehicles are now marked as 'Available'.\n";
} else {
    echo " - Error resetting inventory: " . $conn->error . "\n";
}


echo "\n--- SYSTEM RESET COMPLETE ---\n";
echo "Your 8 sites remain, but all jobs are gone and vehicles are free.\n";
?>