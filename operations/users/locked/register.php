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
    $name = htmlspecialchars(strip_tags(trim($_POST['companyName'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $phoneNumber = htmlspecialchars(strip_tags(trim($_POST['phoneNumber'])));
    $location = htmlspecialchars(strip_tags(trim($_POST['country_selector_code'])));
    $type = htmlspecialchars(strip_tags(trim($_POST['type'])));
    $password = htmlspecialchars(strip_tags(trim($_POST['password'])));
    $confirmPassword = htmlspecialchars(strip_tags(trim($_POST['confirmPassword'])));

    // Validate passwords
    if ($password !== $confirmPassword) {
        throw new Exception("Passwords do not match.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Generate OTP and expiry
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6-digit OTP
    $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Generate access token
    $accessToken = bin2hex(random_bytes(32));

    // Handle file upload
    if (!isset($_FILES['registrationCertificate']) || $_FILES['registrationCertificate']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error in file upload. Please try again.");
    }

    $uploadDir = "../../../vendor_verification_uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $fileTmpPath = $_FILES['registrationCertificate']['tmp_name'];
    $fileName = basename($_FILES['registrationCertificate']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file type
    if ($fileExtension !== "pdf") {
        throw new Exception("Only PDF files are allowed.");
    }

    $newFileName = uniqid() . "_" . $fileName;
    $destPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $destPath)) {
        throw new Exception("Failed to upload the file.");
    }

    // Check for duplicates in the database
    $stmt = $conn->prepare("SELECT * FROM vendors WHERE email = ? OR phone_number = ? OR name = ?");
    $stmt->bind_param("sss", $email, $phoneNumber, $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $vendor = $result->fetch_assoc();
        if ($vendor['email_verified'] == 0 && $vendor['account_verified'] == 0) {
            // Vendor exists but not verified, update OTP and resend email
            // Update vendor details
            $stmtUpdate = $conn->prepare("
                UPDATE vendors SET
                    name = ?,
                    email = ?,
                    phone_number = ?,
                    password = ?,
                    access_token = ?,
                    location = ?,
                    otp = ?,
                    otp_expiry = ?,
                    type = ?,
                    registration_certificate = ?
                WHERE id = ?
            ");
            $stmtUpdate->bind_param(
                "ssssssssssi",
                $name,
                $email,
                $phoneNumber,
                $hashedPassword,
                $accessToken,
                $location,
                $otp,
                $otpExpiry,
                $type,
                $newFileName,
                $vendor['id']
            );
            if (!$stmtUpdate->execute()) {
                throw new Exception("Failed to update vendor information. Please try again.");
            }
            $stmtUpdate->close();

            // Resend email with OTP
            email($email, "Maisha Top OTP", $otp, 1);
            $_SESSION['user'] = 
               [
                'id'=> $vendor['id'],
                'name'=> $name,
                'email'=> $email,
                'phone'=> $phone,
                'accessToken' => $accessToken,
                                'type'=> $type
               ]; 
            // Success response
            $response["success"] = true;
            $response["message"] = "Vendor already registered but not verified. OTP resent to registered email.";
            echo json_encode($response);
            exit();
        } else {
            // Vendor exists and is verified
            throw new Exception("Vendor with the same email, number, or name already exists.");
        }
    }
    $stmt->close();

    // Default values for account status
    $accountActive = 1;
    $accountVerified = 0;
    $emailVerified = 0; // Ensure this column exists in your database

    // Insert data into the database
    $stmt = $conn->prepare("
     INSERT INTO vendors (name, email, phone_number, password, access_token, location, otp, otp_expiry, type, account_active, account_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssssss",
        $name,
        $email,
        $phoneNumber,
        $hashedPassword,
        $accessToken,
        $location,
        $otp,
        $otpExpiry,
        $type,
        $accountActive,
        $accountVerified
    );


    if (!$stmt->execute()) {
        throw new Exception("Failed to register vendor. Please try again.");
    }

    $stmt->close();

    // Send email with OTP
    email($email, "Maisha Top OTP", $otp, 1);
   $_SESSION['user'] = 
               ['name'=> $name,
                'email'=> $email,
                'phone'=> $phone,
                'accessToken' => $accessToken,
                'type'=> $type
               ]; 
    // Success response
    $response["success"] = true;
    $response["message"] = "Vendor registration successful. OTP sent to registered email.";

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
