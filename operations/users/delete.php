<?php
session_start();
// Include database connection
require_once '../../database/dbconnect.php';
require_once '../authorization.php';

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
    $id = intval($_POST['id']);

    // Validate input
    if (empty($id)) {
        throw new Exception("Invalid role ID.");
    }
    validateAdmin($conn, 'all_users');
    // Delete the role from the database
    $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Admin user deleted successfully.";
    } else {
        throw new Exception("Failed to delete user.");
    }
    $stmt->close();

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
