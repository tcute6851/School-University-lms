<?php 
$success = "";
$errors = array();
require_once "config.php";
session_start();

// Check if the user is logged in
if(isset($_SESSION['username'])) {
    $encodedby = $_SESSION['username'];
} else {
    header("Location:indexlogin.php");
    exit; 
}
$link = mysqli_connect("localhost", "root", "", "stdp");
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
if(isset($_GET['studentnumber'])) {
    $studentnumber = $_GET['studentnumber'];
}
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['subjectcode'], $_POST['grade'], $_POST['studentnumber'])) {
        $selectedSubject = $_POST['subjectcode'];
        $newGrade = $_POST['grade'];
        $studentnumber = $_POST['studentnumber'];
        $sql_update_grade = "UPDATE tblgrade SET grade = ? WHERE studentnumber = ? AND subjectcode = ?";
        if ($stmt_update_grade = mysqli_prepare($link, $sql_update_grade)) {
            mysqli_stmt_bind_param($stmt_update_grade, "sss", $newGrade, $studentnumber, $selectedSubject);
            if(mysqli_stmt_execute($stmt_update_grade)) {
                $action = "Update";
                $module = "Grades Management";
                $dateLog = date('Y-m-d');
                $timeLog = date('H:i:sa');
                $ID = $studentnumber;
                $sql_insert_log = "INSERT INTO tbllogs (datelog, timeLog, id, performedby, action, module) VALUES (?, ?, ?, ?, ?, ?)";
                if($stmt_log = mysqli_prepare($link, $sql_insert_log)) {
                    mysqli_stmt_bind_param($stmt_log, "ssssss", $dateLog, $timeLog, $ID, $encodedby, $action, $module);
                    mysqli_stmt_execute($stmt_log);
                    mysqli_stmt_close($stmt_log);
                }
                $success = "<strong><span style='color: green;'>You have been successfully updated a grade.</span></strong>";
            } else {
                $errors[]  = "<strong><span style='color: red;'>Error updating grade.</span></strong>";
            }
            mysqli_stmt_close($stmt_update_grade);
        } else {
            $errors[]  = "<strong><span style='color: red;'>Error updating grade.</span></strong>";
        }
    }
}

if(isset($_GET['studentnumber'], $_GET['subjectcode'])) {
    $studentnumber = $_GET['studentnumber'];
    $subjectcode = $_GET['subjectcode'];

    // Fetch subject description based on subject code
    $sql_select_subject = "SELECT description FROM tblsubject WHERE subjectcode = ?";
    if ($stmt_subject = mysqli_prepare($link, $sql_select_subject)) {
        mysqli_stmt_bind_param($stmt_subject, "s", $subjectcode);
        mysqli_stmt_execute($stmt_subject);
        mysqli_stmt_bind_result($stmt_subject, $description);
        mysqli_stmt_fetch($stmt_subject);
        mysqli_stmt_close($stmt_subject);
    }

    // Fetch grades based on student number and subject code
    $sql_select_grades = "SELECT grade FROM tblgrade WHERE studentnumber = ? AND subjectcode = ?";
    if ($stmt_grades = mysqli_prepare($link, $sql_select_grades)) {
        mysqli_stmt_bind_param($stmt_grades, "ss", $studentnumber, $subjectcode);
        mysqli_stmt_execute($stmt_grades);
        mysqli_stmt_bind_result($stmt_grades, $grade);
        mysqli_stmt_fetch($stmt_grades);
        mysqli_stmt_close($stmt_grades);
    }
}

$sql_select_student = "SELECT lastname, firstname, middlename, yearlevel, course FROM tblstudent WHERE studentnumber = ?";
if ($stmt_student = mysqli_prepare($link, $sql_select_student)) {
    mysqli_stmt_bind_param($stmt_student, "s", $studentnumber);
    mysqli_stmt_execute($stmt_student);
    mysqli_stmt_bind_result($stmt_student, $lastname, $firstname, $middlename, $year, $course);
    mysqli_stmt_fetch($stmt_student);
    mysqli_stmt_close($stmt_student);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Grades - Subject Advising System</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link rel="stylesheet" href="login.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        header {
            background-color: #003366;
            padding: 15px 0;
        }

        nav.topnav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding: 0 20px;
        }

        nav.topnav a {
            color: #ffffff;
            margin: 0 15px;
            font-weight: bold;
        }

        nav.topnav a:hover {
            background-color: #00509e;
            color: #f0f0f0;
            text-decoration: underline;
        }

        nav .brand {
            font-size: 24px;
            color: #ffffff;
        }

        nav .profile-picture-container {
            position: relative;
        }

        nav .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        nav .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ffffff;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        nav .dropdown-content a {
            color: #004d40;
            padding: 10px;
            text-align: center;
            display: block;
        }

        nav .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        nav .profile-picture-container:hover .dropdown-content {
            display: block;
        }

        .notification {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            margin: 10px;
            border-radius: 5px;
            display: none;
        }

        .notification-content {
            display: flex;
            align-items: center;
        }

        .notification-icon {
            margin-right: 10px;
        }

        .footer {
            background-color: #003366;
            color: #fff;
            padding: 20px;
            text-align: center;
            width: 100%;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .social-media img {
            width: 30px;
            height: 30px;
            margin: 0 5px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .updateaccform {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .updateaccform p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .updateaccform form {
            display: flex;
            flex-direction: column;
        }

        .updateaccform form input,
        .updateaccform form select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .updateaccform form input[type="submit"] {
            background-color: #003366;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        .updateaccform form input[type="submit"]:hover {
            background-color: #00509e;
        }

        .updateaccform form a {
            color: #003366;
            text-decoration: none;
            font-weight: bold;
        }

        .updateaccform form a:hover {
            text-decoration: underline;
        }

        .toggle-switch {
            display: inline-block;
            width: 34px;
            height: 20px;
            background-color: #ddd;
            border-radius: 20px;
            position: relative;
            cursor: pointer;
            margin-right: 10px;
        }

        .toggle-switch::after {
            content: '';
            display: block;
            width: 16px;
            height: 16px;
            background-color: #fff;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: 0.3s;
        }

        #showPassword:checked + .toggle-switch::after {
            transform: translateX(14px);
        }

        @media (max-width: 768px) {
            body {
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .container {
                padding: 0 10px;
            }

            .updateaccform {
                padding: 15px;
                margin: 15px 0;
            }

            .updateaccform p {
                font-size: 16px;
            }

            .updateaccform form input,
            .updateaccform form select {
                font-size: 14px;
            }

            .updateaccform form input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
<header>
    <nav class="topnav" id="mytopnav">
        <div class="brand">Celestial Scholar University</div>
        <?php if ($Usertype === 'ADMINISTRATOR'): ?>
            <a href="index.php">Home</a>
            <a href="accounts_management.php">Accounts</a>
            <a href="studentpage.php">Students</a>
            <a href="subject_management.php">Subjects</a>
            <a href="grades-management.php">Grades</a>
        <?php elseif ($Usertype === 'REGISTRAR' || $Usertype === 'STAFF'): ?>
            <a href="index.php">Home</a>
            <a href="studentpage.php">Students</a>
            <a href="subject_management.php">Subjects</a>
            <a href="grades-management.php">Grades</a>
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="viewgrade.php">View Grades</a>
            <a href="viewsubject.php">Subjects to be taken</a>
        <?php endif; ?>
        <div class="profile-picture-container">
            <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-picture">
            <div class="dropdown-content">
                <p>Username: <?php echo $_SESSION['username']; ?></p>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>
<div class="notification" id="notification"></div>
<script>
function displayNotification(message, type) {
        var notification = document.getElementById('notification');
        notification.style.display = 'block';
        notification.innerHTML = '<div class="notification-content">' +
            '<div class="notification-icon ' + type + '"></div>' +
            '<div class="notification-message">' + message + '</div>' +
            '</div>';
        setTimeout(function() {
            notification.style.display = 'none';
        }, 4000);
    }
    <?php
    if (!empty($errors)) {
        echo "displayNotification('<b>" . addslashes(implode("<br>", $errors)) . "</b>', 'error');";
    }
    if (!empty($success)) {
        echo "displayNotification('<b>" . addslashes($success) . "</b>', 'success');";
        echo "setTimeout(function() {
            window.location.href = 'grades-management.php';
        }, 1000);"; 
    }
?>
</script>
    <?php
    // Fetch student information from the database
    $sql_select_student = "SELECT lastname, firstname, middlename, yearlevel, course FROM tblstudent WHERE studentnumber = ?";
    if ($stmt_student = mysqli_prepare($link, $sql_select_student)) {
        mysqli_stmt_bind_param($stmt_student, "s", $studentnumber);
        mysqli_stmt_execute($stmt_student);
        mysqli_stmt_bind_result($stmt_student, $lastname, $firstname, $middlename, $year, $course);
        mysqli_stmt_fetch($stmt_student);
        mysqli_stmt_close($stmt_student);
    }
    ?>    
       <div class="container">
       <div class="updateaccform">
        <h2>Fill Up this form to Update Grades</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="updategrade">
            <p><strong>Name: <?php echo $lastname . ' ' . $firstname . ' ' . $middlename; ?></strong></p> 
            <p><strong>Student Number: <?php echo $studentnumber; ?></strong></p> 
            <p><strong>Year: <?php echo $year; ?></strong></p> 
            <p><strong>Course: <?php echo $course; ?></strong></p> 
            <?php if(isset($subjectcode)): ?>
                <p><strong>Subject Code: <?php echo $subjectcode; ?></strong></p> 
                <p><strong>Description: <?php echo $description; ?></strong></p> 
                <p><strong>Current Grade: <?php echo isset($grade) ? $grade : ''; ?></strong></p> 
            <?php endif; ?>
            <input type="hidden" name="subjectcode" value="<?php echo $subjectcode; ?>">
            <input type="hidden" name="studentnumber" value="<?php echo $studentnumber; ?>">
            <label for="grade"><strong>Grade:</strong></label>
            <select id="grade" name="grade" required> 
                <option value="" disabled selected>Select Grade</option>
                <option value="1.0">1.0</option>
                <option value="1.25">1.25</option>
                <option value="1.50">1.50</option>
                <option value="1.75">1.75</option>
                <option value="2.0">2.0</option>
                <option value="2.25">2.25</option>
                <option value="2.50">2.50</option>
                <option value="2.75">2.75</option>
                <option value="3.0">3.0</option>
            </select><br><br>
            <input type="submit" id="btnsubmit" name="btnsubmit" value="Save">
            <a href="grades-management.php">Cancel</a>
        </form>
    </div>
    </div>
    <footer class="footer">
    <div class="social-media">
        <a href="#"><img src="img/facebook.svg" alt="Facebook"></a>
        <a href="#"><img src="img/twitter.svg" alt="Twitter"></a>
        <a href="#"><img src="img/instagram.svg" alt="Instagram"></a>
    </div>
    <ul>
        <li><a href="#">Terms of Service</a></li>
        <li><a href="#">Privacy Policy</a></li>
    </ul>
</footer>
<script>
        $(document).ready(function(){
            $('#subjectcode').change(function(){
                var selectedSubject = $(this).val();
                var description = $('option:selected', this).data('description');
                $('#description').text(description);
            });
            $('#subjectcode').trigger('change');
        });
        document.getElementById('btnsubmit').addEventListener('click', function(event) {
        var btn = event.target;
        var form = document.getElementById('updategrade');
        var phpErrors = form.querySelector('.php-errors');
        if (form.checkValidity()) {
            btn.value = "Saving please wait...";
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.7';

            setTimeout(function() {
                btn.value = "Save";
                btn.style.pointerEvents = 'auto';
                btn.style.opacity = '1';
                phpErrors.innerHTML = "";
                form.submit();
            }, 5000);
        } else {
            event.preventDefault();
            phpErrors.innerHTML = "Please fill up the form completely.";
        }
    });
    </script>
</body>
</html>
