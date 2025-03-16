<?php
session_start();
// Include database connection and email function
require_once '../../database/dbconnect.php';
require_once '../../resources/email/email.php';
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

    // Verify the user making the request
    if (!isset($_SESSION['user'])) {
        throw new Exception("Unauthorized access.");
    }

    // Sanitize and collect input data
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
    $role = intval($_POST['role']); // Role ID

    validateAdmin($conn, 'all_users');
    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($role)) {
        throw new Exception("All fields are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address provided.");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        throw new Exception("An account with this email already exists.");
    }
    $stmt->close();

    // Create an 8-character password with numbers, letters, and special characters
    $password_plain = generatePassword(8);
    // Hash the password
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    // Generate access token
    $access_token = bin2hex(random_bytes(16));

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO admin (name, email, phone_number, password, access_token, account_active, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $account_active = 1; // Set account as active
    $stmt->bind_param("ssssssi", $name, $email, $phone, $password_hashed, $access_token, $account_active, $role);
    if (!$stmt->execute()) {
        throw new Exception("Failed to create user. Please try again.");
    }
    $stmt->close();

    // Send email with password
    $subject = "Your Analysis Administrative User Account";
    $body = generatePasswordEmailContent($name, $email, $password_plain);
    if (!email($email, $subject, null, $body)) {
        throw new Exception("Failed to send email to the user.");
    }

    // Success response
    $response["success"] = true;
    $response["message"] = "User created successfully.";

} catch (Exception $e) {
    // Error response
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

// Close the database connection
$conn->close();

// Return JSON response
echo json_encode($response);

/**
 * Function to generate a random password
 * @param int $length
 * @return string
 */
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    $password = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }
    return $password;
}

/**
 * Function to generate the email content for the password email
 * @param string $name
 * @param string $email
 * @param string $password
 * @return string
 */
function generatePasswordEmailContent($name, $email, $password) {
    $emailContent = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Welcome to Analysis</title>
    <style>
        body {
            font-family: "Helvetica Neue", Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #005ea5;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .password {
            font-size: 20px;
            font-weight: bold;
            color: #005ea5;
            margin: 20px 0;
            word-break: break-all;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #005ea5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #777777;
            border-top: 1px solid #dddddd;
        }
        .footer a {
            color: #005ea5;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            .header, .footer {
                padding: 10px;
            }
            .password {
                font-size: 18px;
            }
        }
    </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">Welcome to Helahub Analysis</div>
            <div class="content">
                <p>Dear ' . htmlspecialchars($name) . ',</p>
                <p>An account has been created for you as an administrative user at <strong>Analysis</strong>.</p>
                <p>Your login credentials are as follows:</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                <p><strong>Password:</strong></p>
                <div class="password">' . htmlspecialchars($password) . '</div>
                <p>Please log in using the above credentials and change your password immediately after logging in for security purposes.</p>
                <p>If you have any questions or need assistance, feel free to contact our support team.</p>
                <a href="https://analysis.helahub.co/user/login" class="button" style="color: white">Log In to Your Account</a>
                <p>Best regards,<br></p>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' Analysis. All rights reserved.<br>
                <a href="https://helahub.co">Visit our website</a> | <a href="https://helahub.co">Contact Support</a>
            </div>
        </div>
    </body>
    </html>';

    return $emailContent;
}
?>
