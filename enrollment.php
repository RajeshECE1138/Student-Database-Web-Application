<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";  // Change this if needed
$password = "";      // Change this if needed
$database = "user_system"; // Ensure this database exists

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $course = $_POST["course"];
    $semester = $_POST["semester"];
    $address = $_POST["address"];
    $photo = $_FILES["photo"]["name"];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
    
    $username = $_SESSION["username"];

    $sql = "INSERT INTO enrollment (username, full_name, email, phone, course, semester, address, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $username, $full_name, $email, $phone, $course, $semester, $address, $photo);
    
    if ($stmt->execute()) {
        $message = "Enrollment successful!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enrollment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand ms-3">Student Enrollment</span>
    <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
</nav>

<div class="container mt-4">
    <h2>Enroll for the Semester</h2>
    <?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Course</label>
            <input type="text" name="course" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Semester</label>
            <input type="number" name="semester" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Upload Photo</label>
            <input type="file" name="photo" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Enroll</button>
    </form>
</div>
</body>
</html>