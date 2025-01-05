<?php
$errors = array();
$success = "";
$account = array();
require_once "config.php";
include "session-checker.php";
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
sleep(1);

if(isset($_POST['btnsubmit']) && isset($_GET['studentnumber'])) {
    $lastname = $_POST['txtlname'];
    $firstname = $_POST['txtfname'];
    $middlename = $_POST['txtmname'];
    $yearlevel = $_POST['cmbyrlevel'];
    $course = $_POST['cmbcoursetype'];
    $studentnumber = $_GET['studentnumber'];
    $sql = "UPDATE tblstudent SET lastname=?, firstname=?, middlename=?, yearlevel=?, course=? WHERE studentnumber=?";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssss", $lastname, $firstname, $middlename, $yearlevel, $course, $studentnumber);    
        if(mysqli_stmt_execute($stmt)) {
            $date = date("Y-m-d");
            $time = date("h:i:sa");
            $action = "Update";
            $module = "Students Management";
            $performedby = isset($_SESSION['username']) ? $_SESSION['username'] : '';
            $sql_insert = "INSERT INTO tbllogs (datelog, timelog, action, module, id, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                mysqli_stmt_bind_param($stmt_insert, "ssssss", $date, $time, $action, $module, $studentnumber, $performedby);
                if(mysqli_stmt_execute($stmt_insert)) {
                    $success = "<strong><span style='color:green;'>You have successfully updated the student</span></strong>";
                } else {
                    $errors[] = "<font color='red'>Error on inserting logs</font>";
                }
            } else {
                $errors[] = "<font color='red'>Error on preparing insert query</font>";
            }
        } else {
            $errors[] = "<font color='red'>Error updating the account</font>";
        }
    } else {
        $errors[] = "<font color='red'>Error preparing the update query</font>";
    }
}

if(isset($_GET['studentnumber']) && !empty(trim($_GET['studentnumber']))) {
    $sql_select = "SELECT * FROM tblstudent WHERE studentnumber = ?";
    if($stmt_select = mysqli_prepare($link, $sql_select)) {
        mysqli_stmt_bind_param($stmt_select, "s", $_GET['studentnumber']);
        if(mysqli_stmt_execute($stmt_select)) {
            $result = mysqli_stmt_get_result($stmt_select);
            $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            $errors[] = "<font color='red'>Error fetching current account data</font>";
        }
    }
} else {
    $errors[] = "<font color='red'>Account not found</font>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student - Celestial Scholar University Student Portal</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link rel="stylesheet" href="login.css">
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
<div class="container">
    <div class="updateaccform">
    <h2 class="form-title">fill up this form to Update Student</h2>
    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST" id="formupdate">
        <div class="formupdate">
        <div class="php-errors">
                <?php
                if (!empty($login_message)) {
                    echo $login_message;
                }
                ?>
            </div>
          Student number: <input type="number" name="txtstudentnum" value="<?php echo isset($account['studentnumber']) ? $account['studentnumber'] : ''; ?>" readonly><br>
          Lastname: <input type="text" name="txtlname" value="<?php echo isset($account['lastname']) ? $account['lastname'] : ''; ?>" required><br>
Firstname: <input type="text" name="txtfname" value="<?php echo isset($account['firstname']) ? $account['firstname'] : ''; ?>" required><br>
Middlename: <input type="text" name="txtmname" value="<?php echo isset($account['middlename']) ? $account['middlename'] : ''; ?>" required><br>
          Current Course Type: <span><strong><?php echo isset($account['course']) ? $account['course'] : ''; ?></strong></span><br><br>
          <select name="cmbcoursetype" id="cmbcoursetype" required>
    <option value="">--Select Course--</option>
    <option value="BACHELOR OF SCIENCE IN ACCOUNTANCY">Bachelor of Science in Accountancy</option>
    <option value="BACHELOR OF SCIENCE IN FINANCIAL MANAGEMENT">Bachelor of Science in Financial Management</option>
    <option value="BACHELOR OF SCIENCE IN MARKETING MANAGEMENT">Bachelor of Science in Marketing Management</option>
    <option value="BACHELOR OF SCIENCE IN HUMAN RESOURCE MANAGEMENT">Bachelor of Science in Human Resource Management</option>
    <option value="BACHELOR OF SCIENCE IN OPERATIONS MANAGEMENT">Bachelor of Science in Operations Management</option>
    <option value="BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY">Bachelor of Science in Information Technology</option>
    <option value="BACHELOR OF SCIENCE IN COMPUTER SCIENCE">Bachelor of Science in Computer Science</option>
    <option value="BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT">Bachelor of Science in Hospitality Management</option>
    <option value="BACHELOR OF SCIENCE IN TOURISM MANAGEMENT">Bachelor of Science in Tourism Management</option>
    <option value="BACHELOR OF SCIENCE IN MEDICAL TECHNOLOGY">Bachelor of Science in Medical Technology</option>
    <option value="BACHELOR OF SCIENCE IN PHARMACY">Bachelor of Science in Pharmacy</option>
    <option value="BACHELOR OF ARTS IN COMMUNICATION">Bachelor of Arts in Communication</option>
    <option value="BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES">Bachelor of Arts in English Language Studies</option>
    <option value="BACHELOR OF ARTS IN PSYCHOLOGY">Bachelor of Arts in Psychology</option>
    <option value="BACHELOR OF SCIENCE IN CRIMINOLOGY">Bachelor of Science in Criminology</option>
    <option value="BACHELOR OF LAWS">Bachelor of Laws</option>
    <option value="BACHELOR OF ELEMENTARY EDUCATION">Bachelor of Elementary Education</option>
    <option value="BACHELOR OF SECONDARY EDUCATION - ENGLISH">Bachelor of Secondary Education - English</option>
    <option value="BACHELOR OF SECONDARY EDUCATION - MATHEMATICS">Bachelor of Secondary Education - Mathematics</option>
    <option value="BACHELOR OF SECONDARY EDUCATION - SCIENCE">Bachelor of Secondary Education - Science</option>
    <option value="BACHELOR OF SECONDARY EDUCATION - SOCIAL STUDIES">Bachelor of Secondary Education - Social Studies</option>
    <option value="BACHELOR OF SCIENCE IN PHYSICAL THERAPY">Bachelor of Science in Physical Therapy</option>
    <option value="BACHELOR OF SCIENCE IN NURSING">Bachelor of Science in Nursing</option>
    <option value="BACHELOR OF SCIENCE IN RADIOLOGIC TECHNOLOGY">Bachelor of Science in Radiologic Technology</option>
    <option value="BACHELOR OF SCIENCE MEDICAL TECHNOLOGY/ MEDICAL LABORATORY SCIENCE">Bachelor of Science Medical Technology/ Medical Laboratory Science</option>
    <option value="BACHELOR OF SCIENCE IN MIDWIFERY">Bachelor of Science in Midwifery</option>
    <option value="BACHELOR OF SCIENCE IN ENVIRONMENTAL SCIENCE">Bachelor of Science in Environmental Science</option>
    <option value="BACHELOR OF ARTS IN ENGLISH">Bachelor of Arts in English</option>
    <option value="BACHELOR OF SCIENCE IN POLITICAL SCIENCE">Bachelor of Science in Political Science</option>
    <option value="BACHELOR OF SCIENCE IN MATHEMATICS">Bachelor of Science in Mathematics</option>
    <option value="BACHELOR OF LIBRARY AND INFORMATION SCIENCE ">Bachelor of Library and Information Science</option>
</select><br><br>
Current Year Level: <span><strong> <?php echo isset($account['yearlevel']) ? $account['yearlevel'] : ''; ?></strong></span><br><br>
<select name="cmbyrlevel" id="cmbyrlevel" required>
                <option value="">--Select Year Level--</option>
    <option value="FIRST">FIRST</option>
    <option value="SECOND">SECOND</option>
    <option value="THIRD">THIRD</option>
    <option value="FOURTH">FOURTH</option>
    </select><br><br>
        <div class="formupdate">
            <input type="submit" name="btnsubmit" value="Update" class="btn-submit">
            <a href="studentpage.php" class="btn-cancel">Cancel</a>
        </div>
        <div class="php-errors"></div>
    </form>
</div>
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
        function displayNotification(message, type) {
            var notification = document.getElementById('notification');
            notification.style.display = 'block';
            notification.innerHTML = '<div class="notification-content">' +
                '<div class="notification-icon ' + type + '"></div>' +
                '<div class="notification-message">' + message + '</div>' +
                '</div>';
            setTimeout(function() {
                notification.style.display = 'none';
            }, 3000);
        }
        <?php
    if (!empty($errors)) {
        echo "displayNotification('<b>" . addslashes(implode("<br>", $errors)) . "</b>', 'error');";
    }
    if (!empty($success)) {
        echo "displayNotification('<b>" . addslashes($success) . "</b>', 'success');";
      echo "setTimeout(function() {
            window.location.href = 'studentpage.php';
      }, 3000);"; 
    }
    ?>
        document.querySelector('.btn-submit').addEventListener('click', function(event) {
            var btn = event.target;
            var form = document.getElementById('formupdate');
            var phpErrors = form.querySelector('.php-errors');
            if (form.checkValidity()) {
                btn.value = "please wait for updating...";
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.7';
                
                setTimeout(function() {
                    btn.value = "Update";
                    btn.style.pointerEvents = 'auto';
                    btn.style.opacity = '1';
                    phpErrors.innerHTML = "";
                    form.submit();
                }, 5000);
            } else {
                event.preventDefault();
                phpErrors.innerHTML = "";
                var errorMessage = document.createElement('span');
                errorMessage.style.color = 'red';
                errorMessage.textContent = "Please fill up the form completely.";
                phpErrors.appendChild(errorMessage);
            }
        });
    </script>
</body>
</html>
