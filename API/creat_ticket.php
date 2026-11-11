<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
  echo json_encode(["success" => false, "message" => "Login required."]);
  exit;
}

$message = trim($_POST['message'] ?? '');
$rating  = trim($_POST['rating'] ?? '');

if ($message === '' || strlen($message) < 5) {
  echo json_encode(["success" => false, "message" => "Please enter a longer message."]);
  exit;
}

if ($rating !== '' && (!ctype_digit($rating) || (int)$rating < 1 || (int)$rating > 5)) {
  echo json_encode(["success" => false, "message" => "Rating must be 1 to 5."]);
  exit;
}

require_once __DIR__ . "/dbinfo.php"; // uses your existing DB connection

// Expected: $conn = new mysqli(...)
// If your dbinfo.php uses $con or $mysqli, rename below accordingly.
if (!isset($conn)) {
  echo json_encode(["success" => false, "message" => "DB connection not found in dbinfo.php"]);
  exit;
}

$userEmail = $_SESSION['user_email'];

$stmt = $conn->prepare("INSERT INTO support_tickets (user_email, message, rating) VALUES (?, ?, ?)");
$ratingVal = ($rating === '') ? null : (int)$rating;
$stmt->bind_param("ssi", $userEmail, $message, $ratingVal);

if (!$stmt->execute()) {
  echo json_encode(["success" => false, "message" => "Server error."]);
  exit;
}

echo json_encode(["success" => true, "ticket_id" => $stmt->insert_id]);
