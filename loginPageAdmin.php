<?php

@include 'config/config.php';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hardcoded admin credentials
    $admin_email = 'pwucalambaadmin@gmail.com';
    $admin_password = 'admincalambapwu';

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin_email'] = $admin_email;
        header('Location: ../ADMIN-SIDE/admin.php');
        exit();
    } else {
        $error[] = 'Invalid email or password.';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
   <title>ADMIN LOGIN</title>
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
        <h3>WELCOME ADMIN!</h3>
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
        <p>Are you a Student?<a href="loginpage.php">Student</a></p>
        <p>Don't have an account? <a href="signup.php">Register now</a></p>
   </form>
  </div>
</div>