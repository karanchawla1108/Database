<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['usertype'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Database connection
include 'db_dataTable_connect.php';

// Handle POST requests for updating trainer information
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trainerIdEdit'])) {
    $trainerId = $_POST['trainerIdEdit'];
    $trainerName = $_POST['trainerNameEdit'];

    $updateQuery = "UPDATE Trainer SET TrainerName = ? WHERE TrainerID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $trainerName, $trainerId);

    if (!$stmt->execute()) {
        $errorMsg = "Error updating trainer: " . $stmt->error;
    } else {
        $update = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer and Class Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>

<header>
<p>Access: <strong>Admin</strong></p>
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">Gym Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="admin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="membership.php">Membership Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="trainer.php">Trainer Table</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="class.php">Class Table</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="payment.php">Payment Table</a></li>
                </ul>
                <form action="logout.php" method="POST" class="d-flex ms-auto">
                    <button type="submit" class="btn btn-primary">Logout</button>
                </form>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-4">
    <h3>Trainer and Class Table</h3>

    <table class="table table-bordered" id="trainerTable">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Trainer Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "
                SELECT 
                    DISTINCT c.ClassName,
                    t.TrainerName,
                    t.TrainerID
                FROM 
                    Class c
                INNER JOIN 
                    Trainer t ON c.TrainerID = t.TrainerID
            ";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['ClassName']}</td>
                            <td>{$row['TrainerName']}</td>
                            <td>
                                <button 
                                    class='edit btn btn-sm btn-primary' 
                                    data-bs-toggle='modal' 
                                    data-bs-target='#editModal'
                                    data-id='{$row['TrainerID']}'
                                    data-name='{$row['TrainerName']}'>
                                    Edit
                                </button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Error fetching data</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<footer class="bg-dark text-white text-center py-3">
    &copy; 2025 Gym Management System. All rights reserved.
</footer>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Trainer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="trainerIdEdit" id="trainerIdEdit">
                    <div class="mb-3">
                        <label for="trainerNameEdit" class="form-label">Trainer Name</label>
                        <input type="text" class="form-control" id="trainerNameEdit" name="trainerNameEdit" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#trainerTable').DataTable();

        // Populate edit modal fields
        $('.edit').on('click', function() {
            const trainerId = $(this).data('id');
            const trainerName = $(this).data('name');
            
            $('#trainerIdEdit').val(trainerId);
            $('#trainerNameEdit').val(trainerName);
        });
    });
</script>
</body>
</html>
