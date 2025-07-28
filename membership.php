

<?php
// Start session and check login
session_start();

if (!isset($_SESSION['user_email']) || $_SESSION['usertype'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Database connection for login
include 'db_login_connect.php';

$u_email = $_SESSION['user_email'];
$sql = "SELECT `name` FROM `users` WHERE `email` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $u_email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $u_name = $row['name'];
} else {
    $u_name = "User";
}

$stmt->close();

// Database connection for gym database
include 'db_dataTable_connect.php';

// Initialize alert variables
$insert = false;
$update = false;
$delete = false;
$errorMsg = '';

// Handle POST requests for Create and Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['membershipTypeEdit'])) {
        // Update Membership
        $membershipType = $_POST['membershipTypeEdit'];
        $price = $_POST['priceEdit'];
        $duration = $_POST['durationEdit'];

        $sql = "UPDATE membershiptable SET Price = ?, Duration = ? WHERE MembershipType = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dis", $price, $duration, $membershipType);
        if (!$stmt->execute()) {
            $errorMsg = "Error updating membership: " . $stmt->error;
        } else {
            $update = true;
        }
        $stmt->close();
    } else {
        // Insert New Membership
        $membershipType = $_POST['membershipType'];
        $price = $_POST['price'];
        $duration = $_POST['duration'];

        $sql = "INSERT INTO membershiptable (MembershipType, Price, Duration) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $membershipType, $price, $duration);
        if (!$stmt->execute()) {
            $errorMsg = "Error adding membership: " . $stmt->error;
        } else {
            $insert = true;
        }
        $stmt->close();
    }
}

// Handle GET request for Delete
if (isset($_GET['delete'])) {
    $membershipType = $_GET['delete'];
    $sql = "DELETE FROM membershiptable WHERE MembershipType = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $membershipType);
    if (!$stmt->execute()) {
        $errorMsg = "Error deleting membership: " . $stmt->error;
    } else {
        $delete = true;
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
<header>
<p>Access: <strong>Admin</strong><p>
 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">Gym Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Home</a>
                    <li class="nav-item">
                        <a class="nav-link" href="membership.php">Membership Table</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trainer.php">Trainer Table</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="payment.php">Payment Table</a>
                    </li>
                </ul>
                <!-- Logout Button -->
                <form action="logout.php" method="POST" class="d-flex ms-auto">
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
            </div>
        </div>
    </nav>
    <!-- <a href="logout.php">logout</a> -->
</header>
</header>

<main>
    <!-- Alerts -->
    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error:</strong> <?php echo htmlspecialchars($errorMsg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($insert): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success:</strong> Membership added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($update): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <strong>Updated:</strong> Membership updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($delete): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Deleted:</strong> Membership deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Membership Table -->
    <div class="container my-4">
        <h2>Membership Table</h2>
        <table class="table table-bordered" id="membershipTable">
            <thead>
            <tr>
                <th>Membership Type</th>
                <th>Price</th>
                <th>Duration (Months)</th>
              
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM membershiptable";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['MembershipType']); ?></td>
                    <td><?php echo htmlspecialchars($row['Price']); ?></td>
                    <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                    <!-- <td>
                        <button 
                            class="edit btn btn-sm btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal"
                            data-type="<?php echo htmlspecialchars($row['MembershipType']); ?>"
                            data-price="<?php echo htmlspecialchars($row['Price']); ?>"
                            data-duration="<?php echo htmlspecialchars($row['Duration']); ?>">
                            Edit
                        </button>
                        <a href="?delete=<?php echo urlencode($row['MembershipType']); ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td> -->
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        $('#membershipTable').DataTable();
    });
</script>
</body>
</html>
