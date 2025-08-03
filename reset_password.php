<?php
session_start();
include("config.php");

if (!isset($_GET["token"])) {
    echo "<script>alert('Invalid request!'); window.location='forgot_password.php';</script>";
    exit();
}

$token = $_GET["token"];

// Check if token exists and is not expired
$sql = "SELECT * FROM users WHERE reset_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('Invalid token!'); window.location='forgot_password.php';</script>";
    exit();
}

$current_time = date("Y-m-d H:i:s");

if ($user['reset_token_expiry'] < $current_time) {
    echo "<script>alert('Token has expired! Request a new reset link.'); window.location='forgot_password.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Update password and remove reset token
    $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $token);
    $stmt->execute();

    echo "<script>alert('Password reset successful! Please log in.'); window.location='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center">Reset Password</h3>
            <form method="POST">
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                </div>
                <button class="btn btn-success w-100">Update Password</button>
            </form>
            <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>
