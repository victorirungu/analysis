<?php
/**
 * Validates the admin's user ID and access token from the session.
 * Throws an exception if validation fails.
 *
 * @param mysqli $conn The database connection.
 */
function validateAdmin($conn, $requiredPermission) {
    // Check if user ID and access token exist in the session
    if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['accessToken'])) {
        throw new Exception("Unauthorized access. Please log in.");
    }

    $user_id = $_SESSION['user']['id'];
    $access_token = $_SESSION['user']['accessToken'];
    // $role_id = $_SESSION['user']['role_id'];

    // Verify the user and role in the admin table
    $stmt = $conn->prepare("
        SELECT roles.accessibility 
        FROM admin 
        INNER JOIN roles ON admin.role = roles.id
        WHERE admin.id = ? AND admin.access_token = ?
    ");
    $stmt->bind_param("is", $user_id, $access_token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid access token or user ID. Please log in again.");
    }

    $roleData = $result->fetch_assoc();
    $stmt->close();

    // Decode accessibility JSON
    $accessibility = json_decode($roleData['accessibility'], true);

    if (!$accessibility) {
        throw new Exception("Role permissions are not properly configured.");
    }

    // Check if the user has super admin privileges
    if (!empty($accessibility['super_admin']) && $accessibility['super_admin'] === true) {
        return; // Grant access
    }

    // Check if the required permission is set to true
    if (empty($accessibility[$requiredPermission]) || $accessibility[$requiredPermission] !== true) {
        throw new Exception("Permission denied. You do not have access to this resource.");
    }
}

