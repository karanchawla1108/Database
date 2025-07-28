<?php


session_start();
error_reporting(0);


// // Database connection details
// $dbHost = "localhost";
// $dbUser = "root";
// $dbPass = "";
// $dbName = "login";
// $dbport = 3307;

// // Create connection
// $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbport);
// if (!$conn) {
//     die("Connection Failed: " . mysqli_connect_error());
// }

// Database connection for login
include 'db_login_connect.php';

// login form
if(isset($_POST['login'])){
  $u_email = $_POST['email'];
  $u_password = $_POST['password'];

  $sql = "SELECT * FROM `users` WHERE `email` = '$u_email' AND `password` = '$u_password'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_array($result);



  if($row['usertype'] == 'admin'){
    

    //security
    $_SESSION['user_email'] = $u_email;
    $_SESSION['usertype'] = 'admin';
    //path to admin page
    header('location:admin.php');
    exit();

  }else if($row['usertype'] == 'staff'){
    
    $_SESSION['user_email'] = $u_email;
    $_SESSION['usertype'] = 'staff';
    //path to user page
    header('location:user.php');
    exit();

  }else{
      $_SESSION['message'] = 'Invalid email or password';
      header('Location: index.php'); // Redirect back to the login page
      exit();
  }

}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login Form</title>
    <link rel="stylesheet" href="style_index.css" class="src">
</head>
<body>

    
 
    <h2>login Page  </h2>
    
    <header></header>
    <main>


        <!-- Display Error Message -->
      <?php if (isset($_SESSION['message'])): ?>
        <div class="error-message">
            <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Clear the message after displaying
            ?>
        </div>
      <?php endif; ?>







        <form action="" method ="post">
            <!-- Email -->
          <div class="input_deg">
            <label for="">Email</label>
            <input type="email" name="email" required>
          </div>

          <!-- Password -->
          <div class="input_deg">
            <label for="">Password</label>
            <input type="password" name="password" required>
          </div>

          <!--  -->

          <div class="input_deg">
            
            <input type="submit" name="login" value="login">
          </div>





        </form>












    </main>
    <section></section>
    <article></article>
    <footer></footer>
    
</body>
</html>