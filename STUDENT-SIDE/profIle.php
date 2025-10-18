<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

@include '../config/config.php';
session_start();


if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_email'])) {
    header('location:loginpage.php');
    exit();
}


$user_email = $_SESSION['user_email'];


$query = "SELECT firstName, lastName, email, address, department_name, year, phoneNumber, dateofBirth, location, postalCode 
          FROM user_form
          WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    $student = [
        'firstName' => '',
        'lastName' => '',
        'email' => '',
        'department_name' => '',
        'year' => '',
        'address' => '',
        'phoneNumber' => '',
        'dateofBirth' => '',
        'location' => '',
        'postalCode' => ''
    ];
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../STUDENT-SIDE-CSS/profile.css">
    <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
    <script src="../STUDENT-SIDE-JS/profile.js" defer></script>
    <title>Student Profile</title>
</head>
<body>
    

 <nav id="sidebar">
    <ul>
      <li>
        <span class="logo">PWU CALAMBA</span>
        <button onclick=toggleSidebar() id="toggle-btn">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>        </button>
      </li>

      <div class="user-info">
      <img class="profile-pic" src="pictures/coco-martin.jpg" alt="">
      <h3 class="user-name"><?php echo $_SESSION['user_name']; ?></h3>
      <h5 class="user-email"><?php echo $_SESSION['user_email']; ?></h5>
      </div>
      
      <hr>

      <li>
          <a href="../STUDENT-SIDE/homepage.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg>          
            <span>Home</span>
            </a>
        </li>
        <button onclick=toggleSubMenu(this) class="dropdown-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-240v-32q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v32q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm80 0h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>
          <span>Profile</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
        </button>
        <ul class="sub-menu">
          <div>
            <li><a href="../STUDENT-SIDE/profile.php">View Profile</a></li>
            <li><a href="../STUDENT-SIDE/subjects.php">Personal Subject <br> / Schedule</a></li>
            <li><a href="../STUDENT-SIDE/curriculum.php">Personal Curriculum</a></li>
            <li><a href="../STUDENT-SIDE/calendar.php">Calendar of Events</a></li>
            <li><a href="../STUDENT-SIDE/assessment.php">Assessment of Tuition</a></li>
          </div>
        </ul>
      </li>
      <li>
        <button onclick=toggleSubMenu(this) class="dropdown-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="m720-430 80 80v190q0 33-23.5 56.5T720-80H160q-33 0-56.5-23.5T80-160v-560q0-33 23.5-56.5T160-800h220q-8 18-12 38.5t-6 41.5H160v560h560v-270Zm52-174 128 128-56 56-128-128q-21 12-45 20t-51 8q-75 0-127.5-52.5T440-700q0-75 52.5-127.5T620-880q75 0 127.5 52.5T800-700q0 27-8 51t-20 45Zm-152 4q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29ZM160-430v270-560 280-12 22Z"/></svg>          <span>School Features</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
        </button>
        <ul class="sub-menu">
          <div>
              <li><a href="../STUDENT-SIDE/intern.php">Job Opportunities <br> / Internships</a></li>
              <li><a href="../STUDENT-SIDE/orgs.php">Organizations, <br>and Clubs</a></li>
          </div>
        </ul>
      </li>
       <li>
        <button onclick=toggleSubMenu(this) class="dropdown-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M200-120q-33 0-56.5-23.5T120-200q0-33 23.5-56.5T200-280q33 0 56.5 23.5T280-200q0 33-23.5 56.5T200-120Zm480 0q0-117-44-218.5T516-516q-76-76-177.5-120T120-680v-120q142 0 265 53t216 146q93 93 146 216t53 265H680Zm-240 0q0-67-25-124.5T346-346q-44-44-101.5-69T120-440v-120q92 0 171.5 34.5T431-431q60 60 94.5 139.5T560-120H440Z"/></svg>          
          <span class="concern-module">Concerns, and Feedback</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
        </button>
        <ul class="sub-menu">
          <div>
            <li><a href="../STUDENT-SIDE/concern.php">Concerns</a></li>
            <li><a href="../STUDENT-SIDE/feedback.php">Feedback</a></li>
          </div>
      </li>
    </ul>
    <li>
        <a href="../STUDENT-SIDE/health.php">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M320-120v-200H120v-320h200v-200h320v200h200v320H640v200H320Zm80-80h160v-200h200v-160H560v-200H400v200H200v160h200v200Zm80-280Z"/></svg>          
        <span>Health</span>
        </a>
      </li>
        <li>
          <a href="../logout.php">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
          <span>Logout</span>
          </a>
        </li>


  </nav>



    <div class="page-content">
        <header class="site-header">
            <div class="container-header">
                <div class="wrapper-header">
                <img class="pwu-logo" src="../pictures/PWU LOGO.png" alt="PWU Logo">
                <h2 class="h2-pwu">Philippine Women's University Calamba</h2>
            </div>
        </div>
    </header>



  <div class="profile-container">
    <h1 class="profile-title">Personal Information</h1>
      <div class="profile-wrapper">

          <div class="profile-firstname-lastname">
            <label for="firstname">First Name</label>
            <label class="profile-name-lastname-label" for="lastname">Last Name</label>
          </div>

        <div class="profile-name-input">
          <input type="text" id="firstname" name="firstname" 
                value="<?php echo htmlspecialchars($student['firstName']); ?>" disabled>
          <input class="profile-name-lastname-input" type="text" id="lastname" name="lastname" 
                value="<?php echo htmlspecialchars($student['lastName']); ?>" disabled>
        </div>

        <div class="profile-email-label">
          <label>Email</label>
          <label class="profile-email-label-1">Course</label>
        </div>

        <div class="profile-email-input">
          <input type="text" id="email" name="email" 
                value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
          <input class="profile-email-input-1" type="text" id="department" name="department"
                value="<?php echo htmlspecialchars($student['department_name']); ?>" disabled>
        </div>

        <div class="profile-address-label">
          <label for="">Address</label>
          <label class="profile-address-label-1" for="">Year Level</label>
        </div>

        <div class="profile-address-input">
          <input type="text" id="address" name="address" 
                value="<?php echo htmlspecialchars($student['address']); ?>" disabled>
          <input class="profile-address-input-1" type="text" id="year" name="year" 
                value="<?php echo htmlspecialchars($student['year'] ?? '', ENT_QUOTES); ?>" disabled>
        </div>

        <div class="profile-phonenumber-dateofbirth-label">
          <label for="">Phone Number</label>
          <label class="profile-phonenumber-dateofbirth-label-1" for="">Date of Birth</label>
        </div>

        <div class="profile-phonenumber-dateofbirth-input">
          <input type="text" id="phonenumber" name="phonenumber" 
                value="<?php echo htmlspecialchars($student['phoneNumber']); ?>" disabled>
          <input class="profile-phonenumber-dateofbirth-input-1" type="date" id="dateofbirth" name="dateofbirth" 
                value="<?php echo htmlspecialchars($student['dateofBirth']); ?>" disabled>
        </div>

        <div class="profile-location-postalcode-label">
          <label for="">Location</label>
          <label class="profile-lotion-postalcode-label-1" for="">Postal Code</label>
        </div>

        <div class="profile-location-postalcode-input">
          <input type="text" id="location" name="location" 
                value="<?php echo htmlspecialchars($student['location']); ?>" disabled>
          <input class="profile-location-postalcode-input-1" type="text" id="postalcode" name="postalcode" 
                value="<?php echo htmlspecialchars($student['postalCode']); ?>" disabled>
        </div>


      </div>
  </div>



</body>
</html>