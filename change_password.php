<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$database = "user_system";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION["username"];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>New passwords do not match!</div>";
    } else {
        // Get current password hash from DB
        $sql = "SELECT password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Verify old password
        if (!password_verify($old_password, $hashed_password)) {
            $message = "<div class='alert alert-danger'>Incorrect old password!</div>";
        } else {
            // Update password in the database
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE username = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_hashed_password, $username);
            
            if ($update_stmt->execute()) {
                $message = "<div class='alert alert-success'>Password updated successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error updating password. Try again!</div>";
            }
            $update_stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 500px;">
        <h3 class="text-center">Change Password</h3>
        <?php echo $message; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="password" name="old_password" class="form-control" placeholder="Old Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
            </div>
            <button class="btn btn-primary w-100">Update Password</button>
        </form>
        <p class="text-center mt-3"><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</div>

</body>
</html>
