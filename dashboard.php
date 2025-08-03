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
$sql = "SELECT full_name, email, phone, course, semester, photo FROM enrollment WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
//Check if the user has enrolled, otherwise set default values
$full_name = $user['full_name'] ?? 'Not Enrolled';
$email = $user['email'] ?? 'Not Available';
$phone = $user['phone'] ?? 'Not Available';
$course = $user['course'] ?? 'Not Enrolled';
$semester = $user['semester'] ?? 'N/A';
$photo = (!empty($user['photo'])) ? "uploads/".$user['photo'] : "default.jpg"; // Set default image if no photo exists
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3 d-flex justify-content-between">
    <span class="text-light">Welcome, <?php echo $user['email']; ?>!</span>
    <div>
        <a href="change_password.php" class="btn btn-warning me-2">Change Password</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>
<div class="container mt-4">
    <h2>Dashboard Overview</h2>

    <div class="row">
        <!-- Profile Section -->
        <div class="col-md-4">
            <div class="card">
                <img src="uploads/<?php echo $user['photo']; ?>" class="card-img-top" alt="Profile Picture">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $user['full_name']; ?></h5>
                    <p class="card-text">Course: <?php echo $user['course']; ?></p>
                    <p class="card-text">Semester: <?php echo $user['semester']; ?></p>
                    <p class="card-text">Phone: <?php echo $user['phone']; ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Access Links -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5>Enrollments</h5>
                            <p>New & Ongoing</p>
                            <a href="enrollment.php" class="btn btn-light btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>Results</h5>
                            <p>Check Grades</p>
                            <a href="#" class="btn btn-light btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>Payments</h5>
                            <p>View & Pay Fees</p>
                            <a href="#" class="btn btn-light btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <!-- Email Update Section -->
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>Email Update</h5>
                            <p>Update Your Email</p>
                            <a href="#" class="btn btn-light btn-sm">Update</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5>Recent Activities</h5>
                    <ul>
                        <li>Enrolled for Semester <?php echo $user['semester']; ?></li>
                        <li>Last Login: <?php echo date("d M Y, h:i A"); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
