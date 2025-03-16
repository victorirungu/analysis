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
    $role_name = htmlspecialchars(strip_tags(trim($_POST['role_name'])));
    $super_admin = isset($_POST['super_admin']) ? 1 : 0;
    $access = isset($_POST['access']) ? $_POST['access'] : [];

    // Validate inputs
    if (empty($role_name)) {
        throw new Exception("Role name is required.");
    }
    validateAdmin($conn, 'all_roles');
    // Prepare accessibility JSON
    if ($super_admin) {
        // If super admin, set all access to true
        $accessibility = json_encode(["super_admin" => true]);
    } else {
        // Create an associative array with access options set to true
        $accessibilityArray = [];
        foreach ($access as $feature) {
            $accessibilityArray[$feature] = true;
        }
        $accessibility = json_encode($accessibilityArray);
    }

    // Check if role already exists
    $stmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
    $stmt->bind_param("s", $role_name);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        throw new Exception("Role name already exists.");
    }
    $stmt->close();

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO roles (name, accessibility) VALUES (?, ?)");
    $stmt->bind_param("ss", $role_name, $accessibility);
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Role created successfully.";
    } else {
        throw new Exception("Failed to create role.");
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
