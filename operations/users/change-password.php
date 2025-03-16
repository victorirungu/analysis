<?php
session_start();
// Include database connection
require_once '../../database/dbconnect.php';

// Initialize response array
$response = [
    "success" => false,
    "message" => "",
];

header('Content-Type: application/json');

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    // Check if session contains user data
    if (!isset($_SESSION['user'])) {
        throw new Exception("User session not found. Please log in or register first.");
    }
    
    $userid = $_SESSION['user']['id'] ?? null;
    $accessToken = $_SESSION['user']['accessToken'] ?? null;
    $oldPassword = htmlspecialchars(strip_tags(trim($_POST['oldpassword'])));
    $password = htmlspecialchars(strip_tags(trim($_POST['password'])));
    $confirmPassword = htmlspecialchars(strip_tags(trim($_POST['cnfpassword'])));

    // Validate passwords
    if ($password !== $confirmPassword) {
        throw new Exception("Passwords do not match.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    if (!$userid || !$accessToken) {
        throw new Exception("Invalid session data. Please log in or register again.");
    }

    // Check if the data exists and is valid
    $stmt = $conn->prepare("SELECT * FROM vendors WHERE id = ? AND access_token = ?");
    $stmt->bind_param("ss", $userid, $accessToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No matching user found for verification.");
    }
    
      $row = $result->fetch_assoc(); 
    // Access the fields in the row
    $current_password = $row['password']; 
    if (!password_verify($oldPassword, $current_password)) {
        throw new Exception("Incorrect password. Try again");
    }
    $vendor = $result->fetch_assoc();
    $stmt->close();

    // Update password in the database
    $stmtUpdate = $conn->prepare("
        UPDATE vendors 
        SET password = ? WHERE id = ? AND access_token = ?
    ");
    $stmtUpdate->bind_param("sss", $hashedPassword, $userid, $accessToken);

    if (!$stmtUpdate->execute()) {
        throw new Exception("Failed to reset password. Please try again.");
    }
    $stmtUpdate->close();

    // Success response
    $response["success"] = true;
    $response["message"] = "Password Updated Successfully.";

} catch (Exception $e) {
    // Error response
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

// Close the database connection
$conn->close();

// Return JSON response
echo json_encode($response);
?>
