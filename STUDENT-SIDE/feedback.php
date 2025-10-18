<?php
error_reporting(E_ALL);              
ini_set('display_errors', 1);          
ini_set('log_errors', 1);            
ini_set('error_log', __DIR__ . '/error.log');

@include '../config/config.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: loginpage.php');
    exit();
}

$student_email = $_SESSION['user_email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = [];
    for ($i = 1; $i <= 12; $i++) {
        $q[$i] = mysqli_real_escape_string($conn, $_POST["q$i"] ?? 0);
        if ($q[$i] < 1 || $q[$i] > 5) { // validate 1-5
            $message = "All questions must be answered with values 1-5.";
            break;
        }
    }

    if ($message === '') {
        $sql = "INSERT INTO feedback 
                (email, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12) 
                VALUES ('$student_email', '{$q[1]}', '{$q[2]}', '{$q[3]}', '{$q[4]}', '{$q[5]}', '{$q[6]}', '{$q[7]}', '{$q[8]}', '{$q[9]}', '{$q[10]}', '{$q[11]}', '{$q[12]}')";

        if (mysqli_query($conn, $sql)) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../STUDENT-SIDE-CSS/feedback.css">
    <script src="../STUDENT-SIDE-JS/feedback.js" defer></script>
    <title>FAQ</title>
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

    <div class="button-container">
        <button id="btn_faq" class="button-container-faqs">FAQS</button>
        <button id="btn_feedback" class="button-container-feedback">Feedback</button>
    </div>

    <hr>

    <div class="blocks">

    <div id="faq_section" class="faq_section">
            <h1>FAQ</h1>
        
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>What is the purpose of the PWU CDCEC Calamba School Portal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>The school portal serves as a centralized online platform for students and faculty to access academic information, view announcements, and stay updated with school activities.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>Who can use the PWU CDCEC Calamba School Portal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>The portal is designed for both students and teachers of PWU CDCEC Calamba to make it easier to view grades, schedules, and post school-related announcements.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>Is the portal available on mobile devices?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>No. The portal is not yet supported on mobile devices. It is recommended to use a desktop or laptop computer for the best experience.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>How do I access my account in the portal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You can log in using your student ID number as your username and the default password given by the IT team. You can change your password after logging in for security.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>How can I report an error or bug on the portal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>If you notice an error or bug, take a screenshot and report it through the Help & Support page, or inform the IT maintenance group from 3IT1.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>What can I do if I forget my password?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You can click on the “Forgot Password” link on the login page and follow the steps to reset it. If you experience issues, contact the IT support assigned to the portal.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>How secure is the school portal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>The portal uses a secure login system and encrypted data storage to ensure all user information is protected and private.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <span>Who maintains and updates the PWU CDCEC Calamba School Portal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>The PWU CDCEC Calamba School Portal is maintained by 3IT1 students.</p>
                    </div>
                </div>
            </div>            
    </div>

   <div id="feedback_section" class="feedback_section">
    <div class="feedback-container">
        <h2>Feedback</h2>
        <p>To help us improve the student portal, please rate each statement according to your experience. Choose a number from 1 to 5, where 1 indicates Strongly Disagree and 5 indicates Strongly Agree.</p>

        <!-- PHP Message Display -->
        <?php if ($message != ''): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form id="feedbackForm" method="POST" action="">
            <?php 
            $questions = [
                "1. The portal is easy to navigate.",
                "2. The features are user-friendly and easy to understand.",
                "3. It is easy to log in and access my account.",
                "4. The system performs well without frequent bugs or errors.",
                "5. The portal responds quickly to my actions.",
                "6. I can access all the features I need without technical issues.",
                "7. The instructions and guides provided in the portal are clear.",
                "8. The feedback and concern form in the portal is easy to use.",
                "9. I feel comfortable reaching out for help when I experience issues.",
                "10. I would recommend using this student portal to other students.",
                "11. The portal enhances my connection with the school community.",
                "12. The portal provides value and convenience in my student activities."
            ];

            for ($q=1; $q<=12; $q++):
            ?>
            <div class="question">
                <p><?php echo $questions[$q-1]; ?></p>
                <div class="likert-scale">
                    <div class="scale-option">
                        <input type="radio" id="q<?php echo $q; ?>-5" name="q<?php echo $q; ?>" value="5">
                        <label for="q<?php echo $q; ?>-5">5. Strongly Agree</label>
                    </div>
                    <div class="scale-option">
                        <input type="radio" id="q<?php echo $q; ?>-4" name="q<?php echo $q; ?>" value="4">
                        <label for="q<?php echo $q; ?>-4">4. Agree</label>
                    </div>
                    <div class="scale-option">
                        <input type="radio" id="q<?php echo $q; ?>-3" name="q<?php echo $q; ?>" value="3">
                        <label for="q<?php echo $q; ?>-3">3. Neutral</label>
                    </div>
                    <div class="scale-option">
                        <input type="radio" id="q<?php echo $q; ?>-2" name="q<?php echo $q; ?>" value="2">
                        <label for="q<?php echo $q; ?>-2">2. Disagree</label>
                    </div>
                    <div class="scale-option">
                        <input type="radio" id="q<?php echo $q; ?>-1" name="q<?php echo $q; ?>" value="1">
                        <label for="q<?php echo $q; ?>-1">1. Strongly Disagree</label>
                    </div>
                </div>
            </div>
            <?php endfor; ?>

            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
    </div>
</div>

</body>
</html>