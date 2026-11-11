<?php
session_start();
require_once 'dbinfo.php';

$response = ['success' => false, 'message' => '', 'type' => '']; // Added 'type'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['login_email']);
    $password = $_POST['login_password'];

    if (empty($email)) {
        $response['message'] = "Email is required.";
        $response['type'] = "email";
    } elseif (empty($password)) {
        $response['message'] = "Password is required.";
        $response['type'] = "password";
    } else {
        $sql = "SELECT * FROM user_file WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['f_name'];
                $_SESSION['user_role'] = $user['role'];
                $response['success'] = true;
            } else {
                $response['message'] = "Incorrect password.";
                $response['type'] = "password"; // Tell AJAX this is a password error
            }
        } else {
            $response['message'] = "Email does not exist.";
            $response['type'] = "email"; // Tell AJAX this is an email error
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
