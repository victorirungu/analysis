<?php
session_start();
// Include database connection
require_once '../../database/dbconnect.php';
require_once '../../resources/email/email.php';

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

    // Sanitize and collect input data
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address provided.");
    }

    // Generate OTP and expiry
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6-digit OTP
    $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // admin does not exist
        throw new Exception("This email address is not recognized. Kindly proceed to create an account.");
    }
    $stmt->close();

    // Update otp and otp_expiry in the database
    $stmtUpdate = $conn->prepare("
        UPDATE admin 
        SET otp = ?, otp_expiry = ? 
        WHERE email = ?
    ");
    $stmtUpdate->bind_param("sss", $otp, $otpExpiry, $email);

    if (!$stmtUpdate->execute()) {
        throw new Exception("Failed to reset admin password. Please try again.");
    }

    $stmtUpdate->close();

    // Send email with OTP
    if (!email($email, "Maisha Top OTP", $otp, 1)) {
        throw new Exception("Failed to send OTP email. Please try again.");
    }
$_SESSION['user']['email'] = $email;
    // Success response
    $response["success"] = true;
    $response["message"] = "OTP sent to requested email.";

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
