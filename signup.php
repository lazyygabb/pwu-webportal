<?php
@include 'config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all departments for the dropdown
$departments = mysqli_query($conn, "SELECT department_name FROM departments ORDER BY department_name ASC");

if (isset($_POST['submit'])) {
        $required_fields = [
            'firstName', 'lastName', 'email', 'address', 'phoneNumber',
            'year', 'dateofBirth', 'department_name', 'location', 'postalCode',
            'password', 'cpassword'
        ];

    $error = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $error[] = ucfirst($field) . ' is required.';
        }
    }

    if (empty($error)) {
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $department_name = mysqli_real_escape_string($conn, $_POST['department_name']); // now stores the name
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $postalCode = mysqli_real_escape_string($conn, $_POST['postalCode']);
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        $dateofBirth = mysqli_real_escape_string($conn, $_POST['dateofBirth']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];

        if ($password !== $cpassword) {
            $error[] = 'Passwords do not match.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = 'Invalid email address.';
        }

        $check = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error[] = 'This email is already registered.';
        }

        if (empty($error)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $profile_initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
            $profile_picture_url = 'default-profile.png';

            // Insert into user_form
            $insertUser = mysqli_query($conn, "
                INSERT INTO user_form
                (firstName, lastName, email, department_name, year, address, phoneNumber, dateofBirth, location, postalCode, password)
                VALUES 
                ('$firstName', '$lastName', '$email', '$department_name', '$year', '$address', '$phoneNumber', '$dateofBirth', '$location', '$postalCode', '$hashed_password')
            ");

            // Insert into students
            $insertStudent = mysqli_query($conn, "
                INSERT INTO students 
                (firstName, lastName, email, department_name, year, address, phoneNumber, dateofBirth, password, profile_picture_url, profile_initials, status)
                VALUES 
                ('$firstName', '$lastName', '$email', '$department_name', '$year', '$address', '$phoneNumber', '$dateofBirth', '$hashed_password', '$profile_picture_url', '$profile_initials', 'active')
            ");

            if ($insertUser && $insertStudent) {
                echo "<script>alert('Registration successful! Redirecting to login...'); window.location='loginpage.php';</script>";
                exit;
            } else {
                $error[] = "Database error: " . mysqli_error($conn);
            }
        }
    }

    if (!empty($error)) {
        foreach ($error as $msg) {
            echo "<script>alert('$msg');</script>";
        }
    }
}
?>








<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
   <link rel="stylesheet" href="signup.css">
</head>

<body>
    <div class="form-wrapper">
        <div class="form-container">
            <div class="logo-container">
                <img class="logo" src="pictures/PWU LOGO.png" alt="PWU Logo">
            </div>

            <h3>Create Account</h3>

            <?php
            if (!empty($error)) {
                foreach ($error as $msg) {
                    echo '<div class="error-message" style="color:red; text-align:center;">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

           <form id="registrationForm" action="" method="POST">
                <div class="profile-container">
                    <div class="profile-wrapper">

                        <!-- Name -->
                        <div class="profile-firstname-lastname">
                            <label for="firstname">First Name</label>
                            <label class="profile-name-lastname-label" for="lastname">Last Name</label>
                        </div>
                        <div class="profile-name-input">
                            <input type="text" id="firstname" name="firstName" placeholder="Enter first name" required>
                            <input class="profile-name-lastname-input" type="text" id="lastname" name="lastName" placeholder="Enter last name" required>
                        </div>

                        <!-- Email + Course -->
                        <div class="profile-email-label">
                            <label for="email">Email</label>
                            <label class="profile-course-label" for="department_name">Department</label>
                        </div>
                        <div class="profile-email-input" style="display: flex; gap: 10px;">
                            <input type="email" id="email" name="email" placeholder="Enter email address" required>
                           <select name="department_name" id="department_name" required>
                                <option value="" disabled selected>Select Department</option>
                                <?php
                                $query = "SELECT department_name FROM departments ORDER BY department_name ASC";
                                $result = mysqli_query($conn, $query);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . htmlspecialchars($row['department_name']) . '">' 
                                            . htmlspecialchars($row['department_name']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No departments found</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="profile-address-label">
                            <label for="address">Address</label>
                            <label for="year">Year</label>
                        </div>
                        <div class="profile-address-input">
                            <input type="text" id="address" name="address" placeholder="Enter your address" required>
                            <select class="year" id="year" name="year" required>
                                <option value="" disabled selected>Select Year</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>

                        <!-- Phone + DOB -->
                        <div class="profile-phonenumber-dateofbirth-label">
                            <label for="phoneNumber">Phone Number</label>
                            <label class="profile-phonenumber-dateofbirth-label-1" for="dateofbirth">Date of Birth</label>
                        </div>
                        <div class="profile-phonenumber-dateofbirth-input">
                            <input type="tel" id="phonenumber" name="phoneNumber" placeholder="Enter phone number" required>
                            <input class="profile-phonenumber-dateofbirth-input-1" type="date" id="dateofbirth" name="dateofBirth" required>
                        </div>

                        <!-- Location + Postal Code -->
                        <div class="profile-location-postalcode-label">
                            <label for="location">Location</label>
                            <label class="profile-location-postalcode-label-1" for="postalcode">Postal Code</label>
                        </div>
                        <div class="profile-location-postalcode-input">
                            <input type="text" id="location" name="location" placeholder="Enter city/town" required>
                            <input class="profile-location-postalcode-input-1" type="text" id="postalCode" name="postalCode" placeholder="Enter postal code" required>
                        </div>

                        <!-- Password -->
                        <div class="profile-password-label">
                            <label for="password">Password</label>
                        </div>
                        <div class="profile-password-input">
                            <input type="password" id="password" name="password" placeholder="Create a password" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="profile-confirm-password-label">
                            <label for="confirm-password">Confirm Password</label>
                        </div>
                        <div class="profile-confirm-password-input">
                            <input type="password" id="confirm-password" name="cpassword" placeholder="Confirm your password" required>
                        </div>

                        <!-- Submit -->
                        <input class="submitform" type="submit" name="submit" value="REGISTER">

                        <!-- Link -->
                        <div class="login-link">
                            <p>Already have an account? <a href="loginpage.php">Login here</a></p>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
