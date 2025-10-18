<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');



@include 'config/config.php';

session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user_form WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
       
        if (password_verify($password, $row['password'])) {

            $_SESSION['user_name']  = $row['firstName'] . ' ' . $row['lastName'];
            $_SESSION['user_email'] = $row['email'];

           
            if (isset($row['user_type']) && $row['user_type'] === 'admin') {
                $_SESSION['admin_name'] = $row['firstName'];
                header('location:../ADMIN-SIDE/admin.php');
                exit();
            } else {
                header('location:STUDENT-SIDE/homepage.php');
                exit();
            }
        } else {
            $error[] = 'Incorrect password!';
        }
    } else { 
        $error[] = 'No account found with that email.';
    }

    mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
   <title>STUDENT LOGIN</title>
    <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
   <link rel="stylesheet" href="loginpage.css">

</head>
<body>
   
<div class="form-container">
  <div class="form-wrapper">
   <form action="" method="post">
      <div class="center-logo">
        <img class="logo" src="pictures/PWU LOGO.png" alt="">
      </div>
        <h3>WELCOME STUDENT!</h3>
        <?php

      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
        <input type="email" name="email" required placeholder="EMAIL"><br>
        <input type="password" name="password" required placeholder="PASSWORD">
        <input class="button" type="submit" name="submit" value="LOGIN NOW" class="form-btn">
        <p>Don't have an account? <a href="signup.php">Register now</a></p>
   </form>
  </div>
</div>