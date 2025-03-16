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

    // Sanitize and collect input data
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $password = htmlspecialchars(strip_tags(trim($_POST['password'])));

    // Validate inputs
    if (empty($email) || empty($password)) {
        throw new Exception("Email and password are required.");
    }

    // Check if the user exists in the database
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid email or password.");
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify the password
    if (!password_verify($password, $user['password'])) {
        throw new Exception("Invalid email or password.");
    }

    // Check if the account is active
    if ($user['account_active'] == 0) {
        throw new Exception("Your account is inactive. Please contact support.");
    }

    // Fetch role data from the roles table
    $role_id = $user['role']; 
    $stmt = $conn->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $role_result = $stmt->get_result();

    if ($role_result->num_rows === 0) {
        throw new Exception("User role not found.");
    }

    $role = $role_result->fetch_assoc();
    $stmt->close();

    // Decode the accessibility JSON
    $accessibility = json_decode($role['accessibility'], true);

    // Set user data in the session, including role name and accessibility
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'phone_number' => $user['phone_number'],
        'accessToken' => $user['access_token'],
        'role_id' => $role['id'],
        'role_name' => $role['name'],
        'accessibility' => $accessibility,
    ];

    // Success response
    $response["success"] = true;
    $response["message"] = "Login successful.";
    $response["user"] = $_SESSION['user'];

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
