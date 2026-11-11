<?php
require_once 'dbinfo.php';

// Get the email from the AJAX request
$email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';

$response = ['exists' => false];

if (!empty($email)) {
    $sql = "SELECT email FROM user_file WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $response['exists'] = true;
    }
}

// Return JSON format (Marking requirement)
header('Content-Type: application/json');
echo json_encode($response);
?>