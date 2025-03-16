<?php
session_start();
// Include database connection and email function
require_once '../../database/dbconnect.php';
require_once '../../resources/email/email.php';
require_once '../authorization.php';

$response = [
    "success" => false,
    "message" => ""
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }
    validateAdmin($conn, 'all_users');
    // Sanitize and validate input
    $editUserId = isset($_POST['editUserId']) ? (int)$_POST['editUserId'] : 0;
    $editUserName = isset($_POST['editUserName']) ? trim($_POST['editUserName']) : '';
    $editUserEmail = isset($_POST['editUserEmail']) ? trim($_POST['editUserEmail']) : '';
    $editUserRole = isset($_POST['editUserRole']) ? (int)$_POST['editUserRole'] : 0;

    if ($editUserId <= 0) {
        throw new Exception("Invalid user ID.");
    }

    if (empty($editUserName)) {
        throw new Exception("User name cannot be empty.");
    }

    if (empty($editUserEmail) || !filter_var($editUserEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address.");
    }

    if ($editUserRole <= 0) {
        throw new Exception("Invalid role selected.");
    }

    // Check if the admin with given ID exists
    $stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->bind_param("i", $editUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Admin not found.");
    }
    $stmt->close();

    // Check for duplicate email (if needed)
    // Uncomment if you want to ensure no duplicate emails:
    
    $stmtCheck = $conn->prepare("SELECT id FROM admin WHERE email = ? AND id <> ?");
    $stmtCheck->bind_param("si", $editUserEmail, $editUserId);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($resCheck->num_rows > 0) {
        throw new Exception("Email is already in use by another admin.");
    }
    $stmtCheck->close();
    

    // Update the admin record
    $stmtUpdate = $conn->prepare("UPDATE admin SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmtUpdate->bind_param("ssii", $editUserName, $editUserEmail, $editUserRole, $editUserId);
    if (!$stmtUpdate->execute()) {
        throw new Exception("Failed to update admin information. Please try again.");
    }
    $stmtUpdate->close();

    $response["success"] = true;
    $response["message"] = "Admin information updated successfully.";

} catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
