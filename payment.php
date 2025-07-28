<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['usertype'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Database connection
include 'db_dataTable_connect.php';








?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Member Count</title>
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
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membership.php">Membership Table</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trainer.php">Trainer Table</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="class.php">Class Table</a>
                    </li> -->
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

    <main class="container mt-4">
        <h3>Class and Member Count</h3>

        <table class="table table-bordered" id="classTable">
            <thead>
                <tr>
                    <th>PaymentID</th>
                    <th>MemberName</th>
                    <th>ClassName</th>
                    <th>TrainerName</th>
                    <th>MembershipType</th>
                    <th>PaymentDate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                  SELECT 
                    p.PaymentID,
                    CONCAT(m.FirstName, ' ', m.LastName) AS MemberName,
                    c.ClassName,
                    t.TrainerName,
                    m.MembershipType,
                    p.PaymentDate,
                    p.Amount
                FROM 
                    Payment p
                JOIN 
                    Member m ON p.MemberID = m.MemberID
                JOIN 
                    Class c ON p.ClassID = c.ClassID
                JOIN 
                    Trainer t ON c.TrainerID = t.TrainerID;
                ";

                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['PaymentID']}</td>
                                <td>{$row['MemberName']}</td>
                                <td>{$row['ClassName']}</td>
                                <td>{$row['TrainerName']}</td>
                                <td>{$row['MembershipType']}</td>
                                <td>{$row['PaymentDate']}</td>
                                <td>{$row['Amount']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Error fetching data</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <form action="export_csv.php" method="POST">
    <button type="submit" class="btn btn-success">Download CSV</button>
</form>

    </main>
     
    <footer class="bg-dark text-white text-center py-3">
        © 2025 Gym Management System. All rights reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#classTable').DataTable();
        });
    </script>
</body>
</html>