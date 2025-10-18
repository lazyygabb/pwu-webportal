<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

@include '../config/config.php';
session_start();


function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty(trim($word))) {
            $initials .= strtoupper($word[0]);
        }
    }
    return substr($initials, 0, 2);
}

function getProfileColor($name) {
    $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'];
    $colorIndex = ord($name[0]) % count($colors);
    return $colors[$colorIndex];
}

function generateProfilePictureHTML($name) {
    $initials = getInitials($name);
    $backgroundColor = getProfileColor($name);
    
    return "
        <div class='profile-pic-container'>
            <div class='initials-avatar' style='background-color: {$backgroundColor}; font-size: 20px;'>
                {$initials}
            </div>
        </div>
    ";
}



// Redirect if not logged in
if (!isset($_SESSION['user_name'])) {
    header('location:loginpage.php');
    exit();
}

// Get student info from session
$email = $_SESSION['user_email'] ?? null;
if (!$email) die('Error: Email not found in session.');

// Fetch the student
$stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) die("Student not found.");

$student_id    = $student['student_id'];
$department_id = $student['department_id'] ?? null;
$year_level    = $student['year'];
$academic_year = '2025-2026';

// Determine current semester dynamically (example: based on month)
$month = date('n');
if ($month >= 6 && $month <= 11) {
    $semester = '1st Semester';
} else {
    $semester = '2nd Semester';
}

// Fetch assessment forms for this student (department & year)
$assessmentQuery = $conn->prepare("
    SELECT af.*, ps.term, ps.due_date, ps.amount, ps.status
    FROM assessment_forms af
    LEFT JOIN payment_schedules ps ON af.assessment_id = ps.assessment_id
    WHERE af.student_id = ?
    AND af.academic_year = ?
    AND af.semester = ?
    ORDER BY af.assessment_id DESC, ps.due_date ASC
");
$assessmentQuery->bind_param("iss", $student_id, $academic_year, $semester);
$assessmentQuery->execute();
$result = $assessmentQuery->get_result();

$assessmentData = [];
$paymentSchedules = [];

while ($row = $result->fetch_assoc()) {
    if (empty($assessmentData)) $assessmentData = $row;
    if (!empty($row['term'])) {
        $paymentSchedules[] = [
            'term'     => $row['term'],
            'due_date' => $row['due_date'],
            'amount'   => $row['amount'],
            'status'   => $row['status']
        ];
    }
}
$assessmentQuery->close();
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
    <link rel="stylesheet" href="../STUDENT-SIDE-CSS/assessment.css">
    <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
    <script src="../STUDENT-SIDE-JS/assessment.js" defer></script>
    <title>Student - Assessment Form</title>
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
          <a href="calendar.html">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg>          <span>Home</span>
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
          <span class="concern-module">Concerns and Feedback</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
        </button>
        <ul class="sub-menu">
          <div>
              <li><a href="../STUDENT-SIDE/concern.php">Concerns</a></li>
              <li><a href="">Feedback</a></li>
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

<div class="container main-content">
    <h1 class="page-title">Tuition Assessment</h1>

    <div class="action-buttons">
        <button class="btn btn-info" onclick="showTab('important-info')">View Important Information</button>
        <button class="btn btn-secondary" onclick="printStatement()">Print Assessment</button>
    </div>

    <div class="tab-container">
        <div class="tab-nav">
            <button class="tab-button active" onclick="showTab('assessment')">Current Assessment</button>
            <button class="tab-button" onclick="showTab('important-info')">Important Information</button>
        </div>

        <!-- Current Assessment Tab -->
        <div id="assessment-tab" class="tab-content active">
            <?php if(!empty($assessmentData)): ?>
            <div class="student-info">
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">First Name: </span><span><?php echo $student['firstName']; ?></span></div>
                    <div class="info-item"><span class="info-label">Program:</span><span><?php echo $assessmentData['course_code']; ?></span></div>
                    <div class="info-item"><span class="info-label">Semester: :</span><span><?php echo $assessmentData['semester']; ?></span></div>
                    <div class="info-item"><span class="info-label">Last Name:</span><span><?php echo $student['lastName']; ?></span></div>
                    <div class="info-item"><span class="info-label">Year Level:</span><span><?php echo $assessmentData['year_level']; ?></span></div>
                    <div class="info-item"><span class="info-label">Academic Year:</span><span><?php echo $assessmentData['academic_year']; ?></span></div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr><th>Fee Description</th><th>Amount</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Tuition Fee</td><td class="amount">₱<?php echo number_format($assessmentData['tuition_fee'],2); ?></td></tr>
                        <tr><td>Other Fee</td><td class="amount">₱<?php echo number_format($assessmentData['other_fee'],2); ?></td></tr>
                        <tr><td>Misc Fee</td><td class="amount">₱<?php echo number_format($assessmentData['misc_fee'],2); ?></td></tr>
                        <tr><td>Course Fee</td><td class="amount">₱<?php echo number_format($assessmentData['course_fee'],2); ?></td></tr>
                        <tr><td><strong>Total Fee</strong></td><td class="amount">₱<?php echo number_format($assessmentData['total_fee'],2); ?></td></tr>
                    </tbody>
                </table>
            </div>

            <?php if(!empty($paymentSchedules)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr><th>Term</th><th>Due Date</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($paymentSchedules as $schedule): ?>
                        <tr>
                            <td><?php echo $schedule['term']; ?></td>
                            <td><?php echo date('Y/m/d', strtotime($schedule['due_date'])); ?></td>
                            <td class="amount">₱<?php echo number_format($schedule['amount'],2); ?></td>
                            <td><?php echo ucfirst($schedule['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php else: ?>
                <p>No assessment found for Academic Year <?php echo $academic_year; ?>, <?php echo $semester; ?>.</p>
            <?php endif; ?>
        </div>

        <!-- Important Information Tab -->
        <div id="important-info-tab" class="tab-content">
            <div class="important-info">
                <h4>Payment Deadline</h4>
                <p>This is to inform all students that there is NO official payment deadline.
                However, students who are unable to settle their accounts immediately are highly encouraged to secure a Promissory Note at the OSAS.</p>
            </div>
            <div class="info-notice">
                <h4>Payment Methods Accepted</h4>
                <ul>
                    <li>GCash and PayMaya</li>
                    <li>Cash payments at PWU Cashier's Office</li>
                </ul>
            </div>
            <div class="info-notice">
                <h4>Contact Information</h4>
                <p><strong>Cashier's Office:</strong> (02) 8123-4567<br>
                   <strong>Email:</strong> cashier@pwu.edu.ph<br>
                   <strong>Office Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
            </div>
            <div class="important-info">
                <h4>Important Reminders</h4>
                <ul>
                    <li>Keep all payment receipts for your records</li>
                    <li>Contact the Cashier's Office for payment discrepancies</li>
                    <li>Students with outstanding balances may be restricted from taking exams unless provided a Promissory Note</li>
                </ul>
            </div>
        </div>

    </div>
</div>



</div>
</div>
</div>
</body>
</html>