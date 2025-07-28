<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['usertype'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Database connection
include 'db_dataTable_connect.php';

// Set headers for file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// Open a file in memory
$output = fopen('php://output', 'w');

// Add column headers
fputcsv($output, ['PaymentID', 'MemberName', 'ClassName', 'TrainerName', 'MembershipType', 'PaymentDate', 'Amount']);

// Fetch data from database
$query = "
    SELECT 
        p.PaymentID,
        CONCAT(m.FirstName, ' ', m.LastName) AS MemberName,
        c.ClassName,
        t.TrainerName,
        m.MembershipType,
        p.PaymentDate,
        p.Amount
    FROM Payment p
    JOIN Member m ON p.MemberID = m.MemberID
    JOIN Class c ON p.ClassID = c.ClassID
    JOIN Trainer t ON c.TrainerID = t.TrainerID
";

$result = mysqli_query($conn, $query);

// Write data rows to the CSV
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
}

// Close file and terminate script
fclose($output);
exit();
?>
