<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

@include '../config/config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:loginpage.php');
    exit();
}

$name = $_SESSION['user_name'];
$success = '';

if (isset($_POST['submit_clearance']) || isset($_POST['submit_book'])) {
    $preferred_date = mysqli_real_escape_string($conn, $_POST['preferred_date'] ?? '');
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name'] ?? '');
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose'] ?? '');

    // ✅ For booking sessions
    if (isset($_POST['submit_book'])) {
        if (!empty($full_name) && !empty($preferred_date)) {
            $sql = "INSERT INTO health_booking (full_name, preferred_date) VALUES ('$full_name', '$preferred_date')";
            if (mysqli_query($conn, $sql)) {
                $success = "Booking added successfully!";
            } else {
                $success = "Error: " . mysqli_error($conn);
            }
        }
    }

    // ✅ For clearance submissions
    if (isset($_POST['submit_clearance'])) {
        if (isset($_FILES['submitted_file']) && $_FILES['submitted_file']['error'] === 0) {
            $filename = basename($_FILES['submitted_file']['name']);
            $tmp_name = $_FILES['submitted_file']['tmp_name'];

            // ✅ Make sure the folder exists
            $uploadDir = __DIR__ . '/../STUDENT-SIDE/file_uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $target = $uploadDir . $filename;

            if (move_uploaded_file($tmp_name, $target)) {
                // Save to database
                $sql = "INSERT INTO purpose_of_clearance (full_name, purpose, submitted_file) 
                        VALUES ('$full_name', '$purpose', '$filename')";
                if (mysqli_query($conn, $sql)) {
                    $success = "Clearance submitted successfully!";
                } else {
                    $success = "Database Error: " . mysqli_error($conn);
                }
            } else {
                $success = "Error uploading file.";
            }
        } else {
            $success = "Please upload a valid clearance file.";
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
      <link rel="stylesheet" href="../STUDENT-SIDE-CSS/health.css">
      <link rel="icon" type="pwu-logo" href="../pictures/PWU LOGO.png">
      <script src="../STUDENT-SIDE-JS/health.js" defer></script>
      <title>Health</title>
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
            <a href="../STUDENT-SIDE/">
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


    <div class="health-container">
      <div class="buttons">
        <button id="btn_about">About</button>
        <button id="btn_emergency_hotlines">Emergency Hotlines</button>
        <button id="btn_health_announcements">Health Announcements</button>
        <button id="btn_medical_clearances">Medical Clearances</button>
        <button id="btn_mental_health_resources">Mental Health Resources</button>
      </div>

      <hr>
 
    <div class="blocks">
      <div class="about" id="about">
        <h3>About</h3>
        <p>Welcome to the PWU Student Portal's Health Page. Important clinic information, like as office hours and phone numbers for on-campus medical support, is available here. Keep abreast of the most recent health-related information and call emergency hotlines in case you want immediate assistance. For convenience, you can also use the portal to request or submit your medical clearances. To further guarantee students' general wellbeing, this page offers access to counseling services and mental health resources.</p>
      
      <h3>Health Bulletin</h3>
        <div class="bulletin">
          <ul>
            <li><strong>Emergency Hotlines:</strong> Quick access to important numbers for urgent help.</li>
            <li><strong>Health Announcements:</strong> Updates on advisories, events, and medical reminders.</li>
            <li><strong>Medical Clearances:</strong> Upload or request clearance documents online.</li>
            <li><strong>Mental Health Resources:</strong> Counseling, wellness programs, and student support.</li>
          </ul>
        </div>
      
      </div>

      <div class="emergency-hotlines" id="emergency_hotlines">
        <h3>Emergency Hotlines</h3>
        <p>Direct access to emergency numbers can save lives during emergencies. Quick contacts for campus security, police, fire, and medical hotlines are available on the health page. These numbers are available to students who need immediate help both on and off campus.</p>
      
        <div class="top-column">
          <div class="hotline">
            <h4>CALAMBA POSO</h4>
            <p>0961 966 2000 (Smart)</p>
            <p>0917 148 9813 / 0917 103 2834 (Globe)</p>
          </div>
          <div class="hotline">
            <h4>CALAMBA CITY POLICE STATION</h4>
            <p>(0491 545 1694 / 0918 331 8641)</p>
          </div>
          <div class="hotline">
            <h4>CALAMBA BFP</h4>
            <p>(049) 545 1695 / 0945 490 4131 - Calamba BFP</p>
          </div>
          <div class="hotline">
            <h4>CALAMBA (MDDRMD)</h4>
            <p>(049) 545 4119 / 0917 148 9813 / 0929 858 2345</p>
          </div>
        </div>
      
        <div class="hospital-div">
          <h3>HOSPITALS</h3> 
        </div>
    
      <div class="hospital-column">
        <div class="hospitals">
          <h4>JP HOSPITAL</h4>
          <p>(049) 545 0882</p>
        </div>
        <hr>
        <div class="hospitals">
          <h4>CALAMBA DOCTORS HOSPITAL</h4>
          <p>(049) 545 7371</p>
        </div>
        <hr>
        <div class="hospitals">
          <h4>CALAMBA MEDICAL CENTER</h4>
          <p>(049) 502 2228</p>
        </div>
        <hr>
        <div class="hospitals">
          <h4>SAN JOSE TRAULMA</h4>
          <p>(049) 531 7077</p>
        </div>
        <hr>
        <div class="hospitals">
          <h4>ST JOHN HOSPITAL</h4>
          <p>(049) 545 0917</p>
        </div>
        <hr>
        <div class="hospitals">
          <h4>CCMC CANLUBANG</h4>
          <p>(049) 520 5626</p>
        </div>
        <hr>
        <div class="hospitals">
          <h4>PAMANA HOSPITAL</h4>
          <p>(049) 545 6858</p>
        </div>
      </div>

      </div>

      <div class="health-announcements" id="health_announcements">
        <h3>Health Announcements</h3>
        <p>This page contains important information on immunization schedules, medical advisories, and campus health policies. Announcements guarantee that students are always aware of future health-related events and safety procedures. Checking this part on a regular basis keeps the community informed and safe.</p>
      
      <div class="announcement">
        <h4>Free Flu Vaccination Drive – October 15, 2025</h4>
        <p>The PWU Clinic will be offering <strong>free flu vaccines</strong> to all enrolled students on <strong>October 15, 2025</strong>, from <strong>9:00 AM to 3:00 PM</strong> at the University Gym. Students are encouraged to bring their school ID and health clearance form.</p>
      </div>

      <div class="announcement">
        <h4>Mental Health Awareness Week – November 4–8, 2025</h4>
        <p>Join a series of activities focused on mental wellness, including stress management workshops, free counseling sessions, and peer support groups. All events will be held at the Student Wellness Center. Don’t miss this opportunity to prioritize your mental health.</p>
      </div>

      <div class="announcement">
        <h4>Clinic Schedule Update</h4>
        <p>Starting <strong>October 1, 2025</strong>, the PWU Clinic will extend its office hours until <strong>6:00 PM on weekdays</strong>. This aims to accommodate students who may need medical services after their classes. Emergency services will remain available 24/7.</p>
      </div>

      <div class="announcement">
        <h4>COVID-19 Booster Shots</h4>
        <p>Students may avail of <strong>COVID-19 booster vaccinations</strong> at the campus clinic every Wednesday from <strong>10:00 AM to 2:00 PM</strong>. Slots are limited, so early registration through the portal is advised. Bring a valid ID and vaccination card.</p>
      </div>
      
      </div>

    <div class="medical-clearances" id="medical_clearances">
      <h3>Medical Clearances</h3>

      <div class="clearance">
        <h4>Medical Clearance Submission</h4>
        <p>Fill out the form below to request or submit your medical clearance. Make sure your document is signed and valid before uploading.</p>
        <br>
        <form action="" method="post" enctype="multipart/form-data">
          <label for="full_name">Full Name:</label><br>
          <input type="text" id="full_name" name="full_name" required><br><br>

          <label for="purpose">Purpose of Clearance:</label><br>
          <input type="text" id="purpose" name="purpose" required><br><br>

          <label for="submitted_file">Upload Clearance Document:</label><br>
          <input type="file" id="submitted_file" name="submitted_file" required><br><br>

          <button type="submit" name="submit_clearance">Submit Clearance</button>
        </form>
      </div>
    </div>


    <div class="mental-health-resources" id="mental_health_resources">
    <h3>Mental Health Resources</h3>

    <div class="resource">
      <h4>Counseling Services</h4>
      <p>Our Student Wellness Center provides free and confidential counseling sessions. Students can book an appointment online and meet with licensed professionals to discuss personal, academic, or emotional concerns.</p>
      <form action="" method="post">
        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" required><br><br>

        <label for="preferred_date">Preferred Date:</label><br>
        <input type="date" id="preferred_date" name="preferred_date" required><br><br>

        <button type="submit" name="submit_book">Book Session</button>
      </form>
    </div>

    <div class="resource">
      <h4>Wellness Programs</h4>
      <p>The university offers workshops and seminars on stress management, mindfulness, and emotional well-being. Watch out for schedules posted in the announcements section and join to learn practical techniques for self-care.</p>
    </div>

    <div class="resource">
      <h4>Emergency Mental Health Support</h4>
      <p>If you or someone you know is in immediate emotional distress, please call the emergency hotline or visit the clinic right away. Trained professionals are available to provide urgent assistance and guidance.</p>
    </div>
  </div>

    </div>

    </div>

  </body>
  </html>