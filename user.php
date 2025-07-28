<!-- !-- ========================================  PHP   ================================================================--> 

<?php
// Start session and check login
session_start();

if (!isset($_SESSION['user_email']) || $_SESSION['usertype'] != 'staff') {
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




// ==================================================================================================================///
/* Refernce 
 
1. www.php.net. (n.d.). PHP: mysqli::prepare - Manual. [online] Available at: https://www.php.net/manual/en/mysqli.prepare.php.

‌2. www.php.net. (n.d.). PHP: mysqli_stmt::bind_param - Manual. [online] Available at: c
3. www.php.net. (n.d.). PHP: mysqli_stmt::execute - Manual. [online] Available at: https://www.php.net/manual/en/mysqli-stmt.execute.php.


- When you click "Add New Members," it adds information about the new person to the "Member" table and checks for duplicates in the "Attendance" and "Payment" tables.
- Update Existing Members: This changes the details about existing members in the Member, Attendance, and Payment tables.
- It gets rid of member information from the Member table based on their ID.
- In case of mistakes, it keeps track of them and lets you know about them.
- Prepared statements are used to stop SQL attack.


*/

// Handle POST requests for Create and Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['memberIdEdit'])) {
        // Update Member
        $memberId = $_POST['memberIdEdit'];
        $firstName = $_POST['firstNameEdit'];
        $lastName = $_POST['lastNameEdit'];
        $contactNumber = $_POST['contactNumberEdit'];
        $email = $_POST['emailEdit'];
        $membershipType = $_POST['membershipTypeEdit'];
        $classId = $_POST['classIdEdit'];
        $attendanceDate = $_POST['attendanceDateEdit'];
        $paymentDate = $_POST['paymentDateEdit'];

        // Determine amount based on membership type
        $amount = ($membershipType == 'Silver') ? 50 : 100;

        // Update Member table
        $sql = "UPDATE Member SET FirstName=?, LastName=?, ContactNumber=?, Email=?, MembershipType=? WHERE MemberID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $firstName, $lastName, $contactNumber, $email, $membershipType, $memberId);
        if (!$stmt->execute()) {
            $errorMsg = "Error updating Member: " . $stmt->error;
        } else {
            // Update Attendance table
            $sql2 = "UPDATE Attendance SET ClassID=?, AttendanceDate=? WHERE MemberID=?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("isi", $classId, $attendanceDate, $memberId);
            if (!$stmt2->execute()) {
                $errorMsg = "Error updating Attendance: " . $stmt2->error;
            } else {
                // Update Payment table
                $sql3 = "UPDATE Payment SET PaymentDate=?, Amount=? WHERE MemberID=? AND ClassID=?";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("sdii", $paymentDate, $amount, $memberId, $classId);
                if (!$stmt3->execute()) {
                    $errorMsg = "Error updating Payment: " . $stmt3->error;
                } else {
                    $update = true;
                }
                $stmt3->close();
            }
            $stmt2->close();
        }
        $stmt->close();
    } else {
        // Insert New Member
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $contactNumber = $_POST['contactNumber'];
        $email = $_POST['email'];
        $membershipType = $_POST['membershipType'];
        $classId = $_POST['classId'];
        $attendanceDate = $_POST['attendanceDate'];
        $paymentDate = $_POST['paymentDate'];

        // Determine amount based on membership type
        $amount = ($membershipType == 'Silver') ? 50 : 100;

        // Check for duplicate email or contact number
        $checkSql = "SELECT * FROM Member WHERE Email = ? OR ContactNumber = ?";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("ss", $email, $contactNumber);
        $stmtCheck->execute();
        $checkResult = $stmtCheck->get_result();

        if ($checkResult->num_rows > 0) {
            $errorMsg = "A member with this email or contact number already exists.";
        } else {
            // Insert into Member table
            $sql = "INSERT INTO Member (FirstName, LastName, ContactNumber, Email, MembershipType) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $firstName, $lastName, $contactNumber, $email, $membershipType);
            if (!$stmt->execute()) {
                $errorMsg = "Error adding Member: " . $stmt->error;
            } else {
                $memberId = $stmt->insert_id;

                // Insert into Attendance table
                $sql2 = "INSERT INTO Attendance (MemberID, ClassID, AttendanceDate) VALUES (?, ?, ?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("iis", $memberId, $classId, $attendanceDate);
                if (!$stmt2->execute()) {
                    $errorMsg = "Error adding Attendance: " . $stmt2->error;
                } else {
                    // Insert into Payment table
                    $sql3 = "INSERT INTO Payment (MemberID, ClassID, PaymentDate, Amount) VALUES (?, ?, ?, ?)";
                    $stmt3 = $conn->prepare($sql3);
                    $stmt3->bind_param("iisd", $memberId, $classId, $paymentDate, $amount);
                    if (!$stmt3->execute()) {
                        $errorMsg = "Error adding Payment: " . $stmt3->error;
                    } else {
                        $insert = true;
                    }
                    $stmt3->close();
                }
                $stmt2->close();
            }
            $stmt->close();
        }
        $stmtCheck->close();
    }
}

// Handle GET request for Delete
// if (isset($_GET['delete'])) {
//     $memberId = $_GET['delete'];
//     $sql = "DELETE FROM Member WHERE MemberID=?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $memberId);
//     if (!$stmt->execute()) {
//         $errorMsg = "Error deleting Member: " . $stmt->error;
//     } else {
//         $delete = true;
//     }
//     $stmt->close();
// }// (www.php.net, n.d.).
?>


 

<!-- // ==================================================================================================================/// -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin page</title>

    <!-- Reference links for bootstrap and datatable 

     1. Bootstrap (2022). Bootstrap. [online] Getbootstrap.com. Available at: https://getbootstrap.com/.
     2. Datatables.net. (n.d.). DataTables | Table plug-in for jQuery. [online] Available at: https://datatables.net/.


    -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> <!-- Datatable -->
    <link href="style_admin.css" rel="stylesheet"> <!-- Custom CSS -->

</head>
<body>





<!-- ========================================  Header  ============================================================== 
 I used the same header for both user and admin pages. The only difference is the access level.Also used the Bootstrap with static logout out button.  -->

    <header>
    <h3>Welcome, <?php echo htmlspecialchars($u_name); ?></h3>

<p>Access: <strong>Staff</strong><p>
 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="user.php">Gym Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">Home</a>
                        </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="membership.php">Membership Table</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="class.php">Class Table</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trainer.php">Trainer Table</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payment.php">Payment Table</a>
                    </li> -->
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

<!-- ========================================  Header end   ================================================================-->














<!-- ========================================  Main Content  ============================================================== -->

<main>
<!-- Using the PHP include function to include the database connection file to initialize the alert variables. -->
<!-- //initialize alert variables.
      $insert = false;
      $update = false;
      $delete = false;
     when its true , it throw the alert message  -->

<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> <?php echo htmlspecialchars($errorMsg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($insert): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Member added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($update): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Updated!</strong> Member updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($delete): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Deleted!</strong> Member deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>




     
     <!-- Add Member form. -->

   <section>

   <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="my-3">
    <div class="mb-3">
        <label for="firstName" class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstName" name="firstName" required
        pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed">
    </div>
    <div class="mb-3">
        <label for="lastName" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastName" name="lastName" required
        pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed">
    </div>
    <div class="mb-3">
        <label for="contactNumber" class="form-label">Contact Number</label>
        <input type="text" class="form-control" id="contactNumber" name="contactNumber" required
           pattern="[0-9]+" title="Only numbers are allowed">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="membershipType" class="form-label">Membership Type</label>
        <select class="form-control" id="membershipType" name="membershipType" required>
            <option value="Silver">Silver</option>
            <option value="Gold">Gold</option>
        </select>
    </div>
    <!-- Hidden input to store the dynamic amount -->
    <input type="hidden" id="amount" name="amount" value="50">

    <div class="mb-3">
        <label for="classId" class="form-label">Class</label>
        <select class="form-control" id="classId" name="classId" required>
            <?php
            $classQuery = "SELECT ClassID, ClassName FROM Class";
            $classResult = mysqli_query($conn, $classQuery);
            while ($classRow = mysqli_fetch_assoc($classResult)) {
                echo "<option value='" . $classRow['ClassID'] . "'>" . $classRow['ClassName'] . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="attendanceDate" class="form-label">Join Date</label>
        <input type="date" class="form-control" id="attendanceDate" name="attendanceDate" required>
    </div>
    <div class="mb-3">
        <label for="paymentDate" class="form-label">Payment Date</label>
        <input type="date" class="form-control" id="paymentDate" name="paymentDate" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Member</button>
</form>

</section>



<section>


<table class="table table-bordered" id="myTable">
     <thead>
     <tr>
         <th>Member ID</th>
         <th>First Name</th>
         <th>Last Name</th>
         <th>Contact Number</th>
         <th>Email</th>
         <th>Membership Type</th>
         <th>Class</th>
         <th>Attendance Date</th>
         <th>Payment Date</th>
         <th>Amount</th>
         <th>Actions</th>
     </tr>
     </thead>
     <tbody>

     <?php
                 // Using the stored function to display the data in the table.
                
                 /*    CREATE VIEW MemberDetails AS
         SELECT 
             m.MemberID, 
             m.FirstName, 
             m.LastName, 
             m.ContactNumber, 
             m.Email, 
             m.MembershipType, 
             c.ClassName, 
             MAX(a.AttendanceDate) AS AttendanceDate, 
             p.PaymentDate, 
             p.Amount
         FROM 
             Member m
         LEFT JOIN Attendance a ON m.MemberID = a.MemberID 
         LEFT JOIN Payment p ON m.MemberID = p.MemberID 
         LEFT JOIN Class c ON a.ClassID = c.ClassID
         WHERE 
             p.PaymentDate = (SELECT MAX(PaymentDate) 
                              FROM Payment 
                              WHERE MemberID = m.MemberID)
         GROUP BY 
             m.MemberID, 
           m.FirstName, 
           m.LastName, 
           m.ContactNumber, 
           m.Email, 
           m.MembershipType, 
           c.ClassName, 
           p.PaymentDate, 
           p.Amount;   */
     $sql = "
     SELECT * FROM MemberDetails
 

 "; 
 // Using the stored function to display the data in the table. Using th lopp to display the data in the table.
     $result = mysqli_query($conn, $sql);
     while ($row = mysqli_fetch_assoc($result)) {
         echo "<tr>
             <td>{$row['MemberID']}</td>
             <td>{$row['FirstName']}</td>
             <td>{$row['LastName']}</td>
             <td>{$row['ContactNumber']}</td>
             <td>{$row['Email']}</td>
             <td>{$row['MembershipType']}</td>
             <td>{$row['ClassName']}</td>
             <td>{$row['AttendanceDate']}</td>
             <td>{$row['PaymentDate']}</td>
             <td>{$row['Amount']}</td>
             <td>
         <button 
              class='edit btn btn-sm btn-primary' 
              data-bs-toggle='modal' 
              data-bs-target='#editModal'
              data-id='{$row['MemberID']}'
              data-firstname='{$row['FirstName']}'
              data-lastname='{$row['LastName']}'
              data-contact='{$row['ContactNumber']}'
              data-email='{$row['Email']}'
              data-membership='{$row['MembershipType']}'
              data-class='{$row['ClassName']}'
              data-attendance='{$row['AttendanceDate']}'
              data-payment='{$row['PaymentDate']}'
              data-amount='{$row['Amount']}'>
              Edit
         </button>
          
                
             </td>
         </tr>";
     }
     ?>
     </tbody>
 </table>




</section>


</main> 
<!-- ========================================  Main End ============================================================== -->







<!-- ========================================  Footer Content  ============================================================== -->


<footer>
<div class="card">
<div class="card-header">
 Featured
</div>
<div class="bg-dark text-white text-center py-3">
 <h5 class="card-title">Gym Management</h5>
 <p class="card-text">&copy; 2025 Gym Management System. All rights reserved.</p>
</div>
</div>
</footer>



<!-- ========================================  Footer Content  ============================================================== -->







<!-- ========================================  Modal Start  ============================================================== -->

<!-- Modal for editing the member details. It is used to update and delete the member details in the database. -->




<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
 <div class="modal-dialog">
     <div class="modal-content">
         <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
             <div class="modal-header">
                 <h5 class="modal-title" id="editModalLabel">Edit Member</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <input type="hidden" name="memberIdEdit" id="memberIdEdit">
                 <div class="mb-3">
                     <label for="firstNameEdit" class="form-label">First Name</label>
                     <input type="text" class="form-control" id="firstNameEdit" name="firstNameEdit" required>
                 </div>
                 <div class="mb-3">
                     <label for="lastNameEdit" class="form-label">Last Name</label>
                     <input type="text" class="form-control" id="lastNameEdit" name="lastNameEdit" required>
                 </div>
                 <div class="mb-3">
                     <label for="contactNumberEdit" class="form-label">Contact Number</label>
                     <input type="text" class="form-control" id="contactNumberEdit" name="contactNumberEdit" required>
                 </div>
                 <div class="mb-3">
                     <label for="emailEdit" class="form-label">Email</label>
                     <input type="email" class="form-control" id="emailEdit" name="emailEdit" required>
                 </div>
                 <div class="mb-3">
                     <label for="membershipTypeEdit" class="form-label">Membership Type</label>
                     <select class="form-control" id="membershipTypeEdit" name="membershipTypeEdit" required>
                         <option value="Silver">Silver</option>
                         <option value="Gold">Gold</option>
                     </select>
                 </div>
                 <div class="mb-3">
                     <label for="classIdEdit" class="form-label">Class</label>
                     <select class="form-control" id="classIdEdit" name="classIdEdit" required>
                         <?php
                         $classQuery = "SELECT ClassID, ClassName FROM Class";
                         $classResult = mysqli_query($conn, $classQuery);
                         while ($classRow = mysqli_fetch_assoc($classResult)) {
                             echo "<option value='" . $classRow['ClassID'] . "'>" . $classRow['ClassName'] . "</option>";
                         }
                         ?>
                     </select>
                 </div>
                 <div class="mb-3">
                     <label for="attendanceDateEdit" class="form-label">Attendance Date</label>
                     <input type="date" class="form-control" id="attendanceDateEdit" name="attendanceDateEdit" required>
                 </div>
                 <div class="mb-3">
                     <label for="paymentDateEdit" class="form-label">Payment Date</label>
                     <input type="date" class="form-control" id="paymentDateEdit" name="paymentDateEdit" required>
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



<!-- ========================================  Modal End  ============================================================== -->


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="script.js"></script>
</body>
</html>