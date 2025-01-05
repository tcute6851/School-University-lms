<?php
$errors = array();
$success = "";
require_once "config.php";
include("session-checker.php");
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
sleep(1);
if (isset($_POST['btnsubmit'])) {
    if(empty($_POST['txtsubjectcode']) || empty($_POST['txtdescription']) || empty($_POST['cmbunit']) || empty($_POST['cmbcoursetype'])) {
        $errors[] = "Please enter all required fields.";
    } else {
        $sql = "SELECT * FROM tblsubject WHERE subjectcode = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "s", $_POST['txtsubjectcode']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 0) {
            mysqli_stmt_close($stmt);
            $date = date("Y-m-d");
            $prerequisite1 = empty($_POST['prerequisite1']) ? NULL : $_POST['prerequisite1'];
            $prerequisite2 = empty($_POST['prerequisite2']) ? NULL : $_POST['prerequisite2'];
            $prerequisite3 = empty($_POST['prerequisite3']) ? NULL : $_POST['prerequisite3'];
            $sql = "INSERT INTO tblsubject (subjectcode, description, unit, course, prerequisite1, prerequisite2, prerequisite3, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ssissssss", $_POST['txtsubjectcode'], $_POST['txtdescription'], $_POST['cmbunit'], $_POST['cmbcoursetype'], $prerequisite1, $prerequisite2, $prerequisite3, $_SESSION['username'], $date);       
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                $date = date("Y-m-d");
                $time = date("H:i:s");
                $action = "Create";
                $module = "Subjects Management";
                $sql = "INSERT INTO tbllogs (datelog, timelog, action, module, id, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_POST['txtsubjectcode'], $_SESSION['username']);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "<strong><span style='color:green;'>You have successfully created a new Subject</span></strong>";
                } else {
                    $errors[] = "<font color='red'>Error on Insert Log Statement</font>";
                }
            } else {
                $errors[] = "<strong><span style='color: red;'>Error on adding new subject</span></strong>";
            }
        } else {
            $errors[] = "<strong><span style='color: red;'>Subject code is already in use</span></strong>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Subject - Celestial Scholar University Student Portal</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link rel="stylesheet" href="login.css">
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

        .createaccform {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .createaccform p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .createaccform form {
            display: flex;
            flex-direction: column;
        }

        .createaccform form input,
        .createaccform form select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .createaccform form input[type="submit"] {
            background-color: #003366;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        .createaccform form input[type="submit"]:hover {
            background-color: #00509e;
        }

        .createaccform form a {
            color: #003366;
            text-decoration: none;
            font-weight: bold;
        }

        .createaccform form a:hover {
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

            .createaccform {
                padding: 15px;
                margin: 15px 0;
            }

            .createaccform p {
                font-size: 16px;
            }

            .createaccform form input,
            .createaccform form select {
                font-size: 14px;
            }

            .createaccform form input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
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
<br><br>
<body>
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
            window.location.href = 'subject_management.php';
        }, 1000);"; 
    }
?>
</script>
<div class="container">
<div class="createaccform">
    <p>Fill up this form and submit in order to add a new subject</p>
    <br>
    <form id="addsubject" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <?php if (!empty($errors)) : ?>
            <div class="php-errors">
                <?php echo implode("<br>", $errors); ?>
            </div>
        <?php endif; ?>
        Subject Code: <input type="text" name="txtsubjectcode" required> <br>
        Description: <input type="text" name="txtdescription" required> <br>
        Pre requisite 1:
        <select name="prerequisite1" id="prerequisite1">
            <option value="">--Select Course First--</option>
        </select><br><br>
        Pre requisite 2:
        <select name="prerequisite2" id="prerequisite2">
            <option value="">--Select Course First--</option>
        </select><br><br>
        Pre requisite 3:
        <select name="prerequisite3" id="prerequisite3">
            <option value="">--Select Course First--</option>
        </select><br><br>
        Number of Unit:
        <select name="cmbunit" id="cmbunit" required>
            <option value="">--Select Unit--</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="5">5</option>
        </select><br><br>
        Course:
        <select name="cmbcoursetype" id="cmbcoursetype" required>
    <option value="">--Select Course--</option>
    <option value="BACHELOR OF SCIENCE IN COMPUTER SCIENCE">Bachelor of Science in Computer Science</option>
    <option value="BACHELOR OF SCIENCE IN ACCOUNTANCY">Bachelor of Science in Accountancy</option>
    <option value="BACHELOR OF SCIENCE IN FINANCIAL MANAGEMENT">Bachelor of Science in Financial Management</option>
    <option value="BACHELOR OF SCIENCE IN MARKETING MANAGEMENT">Bachelor of Science in Marketing Management</option>
    <option value="BACHELOR OF SCIENCE IN HUMAN RESOURCE MANAGEMENT">Bachelor of Science in Human Resource Management</option>
    <option value="BACHELOR OF SCIENCE IN OPERATIONS MANAGEMENT">Bachelor of Science in Operations Management</option>
    <option value="BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY">Bachelor of Science in Information Technology</option>
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
        <input type="submit" name="btnsubmit" class="btnsubmit" value="Add New Subject">
        <a href="subject_management.php">Cancel</a>
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
     document.querySelector('.btnsubmit').addEventListener('click', function(event) {
        var btn = event.target;
        var form = document.getElementById('addsubject');
        var phpErrors = form.querySelector('.php-errors');
        if (form.checkValidity()) {
            btn.value = "please wait Verifying...";
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.7';
            
            setTimeout(function() {
                btn.value = "Add New Account";
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
document.getElementById('cmbcoursetype').addEventListener('change', function() {
    var course = this.value;
    var prerequisites = document.querySelectorAll('[name^="prerequisite"]');
    
    if (course !== '') {
        prerequisites.forEach(function(prerequisite) {
            fetchPrerequisites(course, prerequisite);
        });
    } else {
        prerequisites.forEach(function(prerequisite) {
            clearPrerequisites(prerequisite);
        });
    }
});

function fetchPrerequisites(course, prerequisite) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() { 
        if (xhr.readyState == 4 && xhr.status == 200) {
            prerequisite.innerHTML = xhr.responseText;
        }
    };
    xhr.open("GET", "get_prerequisites.php?course=" + encodeURIComponent(course), true);
    xhr.send();
}

function clearPrerequisites(prerequisite) {
    prerequisite.innerHTML = "<option value=''>--Select Course First--</option>";
}
</script>
</body>
</html>
