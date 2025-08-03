<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";  // Change this if needed
$password = "";      // Change this if needed
$database = "user_system";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_email = $_POST["email"];

    // Check if the email already exists for another user
    $check_sql = "SELECT username FROM users WHERE email = ? AND username != ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $new_email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Error: This email is already registered with another account.";
    } else {
        // Proceed with the update if email is unique
        $sql = "UPDATE users SET email = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_email, $username);
        
        if ($stmt->execute()) {
            $message = "Details updated successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}


// Fetch current details
$sql = "SELECT email FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Mobile/Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand ms-3">Update Email</span>
    <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
</nav>

<div class="container mt-4">
    <h2>Update Email</h2>
    <?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
