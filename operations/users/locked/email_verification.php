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

    // Get OTP from POST request
    $otp = htmlspecialchars(strip_tags(trim($_POST['otp'])));

    if (empty($otp)) {
        throw new Exception("OTP is required.");
    }

    // Retrieve user data from session
    $email = $_SESSION['user']['email'] ?? null;
    $accessToken = $_SESSION['user']['accessToken'] ?? null;

    if (!$email || !$accessToken) {
        throw new Exception("Invalid session data. Please log in or register again.");
    }

    // Check if the OTP exists and is valid
    $stmt = $conn->prepare("SELECT otp, otp_expiry,id FROM admin WHERE email = ? AND access_token = ?");
    $stmt->bind_param("ss", $email, $accessToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No matching user found for verification.");
    }

    $admin = $result->fetch_assoc();
    $stmt->close();

    // Verify OTP
    if ($admin['otp'] !== $otp) {
        throw new Exception("Invalid OTP. Please try again.");
    }

    // Check OTP expiry
    $currentDateTime = date('Y-m-d H:i:s');
    if (strtotime($currentDateTime) > strtotime($admin['otp_expiry'])) {
        throw new Exception("OTP has expired. Please request a new OTP.");
    }

    // Update email_verified and account_verified status in the database
    $stmtUpdate = $conn->prepare("
        UPDATE admin 
        SET otp = NULL, otp_expiry = NULL 
        WHERE email = ? AND access_token = ?
    ");
    $stmtUpdate->bind_param("ss", $email, $accessToken);

    if (!$stmtUpdate->execute()) {
        throw new Exception("Failed to verify email. Please try again.");
    }
    $stmtUpdate->close();

    $_SESSION['user']['id'] = $admin['id'];
    

    // Success response
    $response["success"] = true;
    $response["message"] = "Email verification successful.";

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
