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

$email = $_SESSION['user_email'] ?? null;
if (!$email) die('Error: Email not found in session.');

// Fetch user info from user_form
$stmt = $conn->prepare("SELECT * FROM user_form WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$department_name = $user['department_name'] ?? null;
if (!$department_name) die('Error: No department assigned to this user.');

// Find department using LIKE for flexibility
$deptStmt = $conn->prepare("
    SELECT department_id, department_code, department_name 
    FROM departments 
    WHERE LOWER(department_name) LIKE CONCAT('%', LOWER(?), '%')
    LIMIT 1
");
$deptStmt->bind_param("s", $department_name);
$deptStmt->execute();
$deptResult = $deptStmt->get_result();
$deptRow = $deptResult->fetch_assoc();
$deptStmt->close();

$department_id = $deptRow['department_id'] ?? null;
$department_code = $deptRow['department_code'] ?? '';
if (!$department_id) die("Error: Department not found.");

// Fetch all subjects for this department
$subjectQuery = $conn->prepare("
    SELECT s.*, d.department_name, d.department_code
    FROM subjects s
    INNER JOIN departments d ON s.department_id = d.department_id
    WHERE s.department_id = ?
    ORDER BY 
        CASE s.year_level 
            WHEN '1st Year' THEN 1
            WHEN '2nd Year' THEN 2
            WHEN '3rd Year' THEN 3
            WHEN '4th Year' THEN 4
            ELSE 5
        END ASC,
        CASE s.semester 
            WHEN '1st Semester' THEN 1
            WHEN '2nd Semester' THEN 2
            ELSE 3
        END ASC,
        s.subject_name ASC
");
$subjectQuery->bind_param("i", $department_id);
$subjectQuery->execute();
$resultSubjects = $subjectQuery->get_result();
$subjects = $resultSubjects->fetch_all(MYSQLI_ASSOC);
$subjectQuery->close();

// Group subjects by year_level and semester
$groupedSubjects = [];
foreach ($subjects as $subj) {
    $year = $subj['year_level'] ?? 'Unknown Year';
    $sem = $subj['semester'] ?? 'Unknown Semester';
    $groupedSubjects[$year][$sem][] = $subj;
}

// Sort years and semesters
$yearOrder = ['1st Year'=>1,'2nd Year'=>2,'3rd Year'=>3,'4th Year'=>4,'Unknown Year'=>5];
$semesterOrder = ['1st Semester'=>1,'2nd Semester'=>2,'Unknown Semester'=>3];

uksort($groupedSubjects, fn($a,$b) => ($yearOrder[$a] ?? 5) - ($yearOrder[$b] ?? 5));
foreach ($groupedSubjects as &$yearData) {
    uksort($yearData, fn($a,$b) => ($semesterOrder[$a] ?? 3) - ($semesterOrder[$b] ?? 3));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="../STUDENT-SIDE-CSS/curriculum.css">
    <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
    <script src="../STUDENT-SIDE-JS/curriculum.js" defer></script>
    <title>Personal Curriculum</title>
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
          <span class="concern-module">Concerns and Feedback</span>
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

       <h2 class="curriculum">Personal Curriculum</h2>
<div class="tables-wrapper">
<?php if (empty($groupedSubjects)): ?>
    <p>No subjects found for your department.</p>
<?php else: ?>
    <?php foreach ($groupedSubjects as $yearLevel => $yearData): ?>
        <?php foreach ($yearData as $semester => $rows): ?>
            <div class="table-box">
                <div class="year-semester">
                    <h4 class="year-semester"><?= htmlspecialchars($yearLevel) ?></h4>
                    <h4 class="year-semester"><?= htmlspecialchars($semester) ?></h4>
                </div>
                <table border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Units</th>
                            <th>Year Level</th>
                            <th>Semester</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr class="<?= ($row['status'] ?? '') !== 'active' ? 'inactive' : '' ?>">
                                <td><?= htmlspecialchars($row['department_code'] ?? $department_code) ?></td>
                                <td><?= htmlspecialchars($row['subject_code']) ?></td>
                                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                <td><?= htmlspecialchars($row['units']) ?></td>
                                <td><?= htmlspecialchars($row['year_level'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['semester'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['status'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</body>
</html>
