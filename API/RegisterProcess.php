<?php
session_start();
require_once 'dbinfo.php';

// Prepare JSON response
$response = ['success' => false, 'message' => '', 'type' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture ALL fields from the form
    $f_name = mysqli_real_escape_string($conn, $_POST['f_name']);
    $s_name = mysqli_real_escape_string($conn, $_POST['s_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $conf_pass = $_POST['confirm_password'];
    $addr1 = mysqli_real_escape_string($conn, $_POST['address1']);
    $addr2 = mysqli_real_escape_string($conn, $_POST['address2']); // Optional
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);

    // 2. Validation
    $passwordPattern = "/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";

    $email_domain = "@gmail.com";

    if (!preg_match($passwordPattern, $pass)) {
        $response['message'] = "Password must be at least 8 chars, include a letter, a number, and a special character.";
        $response['type'] = "password";
    } elseif (substr($email, -strlen($email_domain)) !== $email_domain) {
        $response['message'] = "Only Gmail addresses (@gmail.com) are allowed.";
        $response['type'] = "email";
    } elseif ($pass !== $conf_pass) {
        $response['message'] = "Passwords do not match.";
        $response['type'] = "password";
    } else {
        // 3. Check if Email Exists
        $checkSql = "SELECT email FROM user_file WHERE email = '$email'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            $response['message'] = "This email is already registered.";
            $response['type'] = "email";
        } else {
            // 4. Secure Hash & Insert
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO user_file (f_name, s_name, email, password, address1, address2, city, state, zip, role) 
                    VALUES ('$f_name', '$s_name', '$email', '$hashed_password', '$addr1', '$addr2', '$city', '$state', '$zip', 'customer')";

            if ($conn->query($sql) === TRUE) {
                // Auto-login the user
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $f_name;
                $_SESSION['user_role'] = 'customer';
                $response['success'] = true;
            } else {
                $response['message'] = "Database Error: " . $conn->error;
            }
        }
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($response);
exit();
