<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Generate token and set 1-minute expiry
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 minute"));

        // Update token in database
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Redirect to reset password page with token
        header("Location: reset_password.php?token=" . $token);
        exit();
    } else {
        echo "<script>alert('No account found with that email!'); window.location='forgot_password.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center">Forgot Password</h3>
            <form method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <button class="btn btn-primary w-100">Reset Password</button>
            </form>
            <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>
