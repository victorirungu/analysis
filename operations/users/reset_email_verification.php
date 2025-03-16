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

    // Get OTP from POST request
    $otp = htmlspecialchars(strip_tags(trim($_POST['otp'])));

    if (empty($otp)) {
        throw new Exception("OTP is required.");
    }

    // Retrieve user data from session
    $email = $_SESSION['user']['email'] ?? null;

    if (!$email) {
        throw new Exception("Invalid email data. Please log in or register again.");
    }

    // Check if the OTP exists and is valid
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
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
    $stmtUpdate->bind_param("ss", $email, $admin['access_token']);

    if (!$stmtUpdate->execute()) {
        throw new Exception("Failed to verify email. Please try again.");
    }
    $stmtUpdate->close();

    // Fetch role data from roles table
    $role_id = $admin['role']; // Assuming 'role' in 'admin' table is the role ID

    $stmtRole = $conn->prepare("SELECT * FROM roles WHERE id = ?");
    $stmtRole->bind_param("i", $role_id);
    $stmtRole->execute();
    $role_result = $stmtRole->get_result();

    if ($role_result->num_rows === 0) {
        throw new Exception("User role not found.");
    }

    $role = $role_result->fetch_assoc();
    $stmtRole->close();

    // Decode accessibility JSON
    $accessibility = json_decode($role['accessibility'], true);

    // Update session to reflect verified status and include role data
    $_SESSION['user'] = [
        'id' => $admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
        'phone_number' => $admin['phone_number'],
        'accessToken' => $admin['access_token'],
        'role_id' => $role['id'],
        'role_name' => $role['name'],
        'accessibility' => $accessibility,
    ];

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
