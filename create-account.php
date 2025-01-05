<?php
$errors = array(); // Initialize an array to store errors
$success = ""; // Initialize a variable to store success message
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
    if (empty($_POST['txtusername']) || empty($_POST['txtpassword']) || empty($_POST['cmbaccountType'])) {
        $errors[] = "Please enter both username and password.";
    } else {
        $sql = "SELECT * FROM tblaccounts WHERE username = '{$_POST['txtusername']}'";
        $result = mysqli_query($link, $sql);
        if ($result) {
            if (mysqli_num_rows($result) == 0) {
                $status = "ACTIVE";
                $date = date("Y-m-d");
                $time = date("h:i:sa");
                $sql = "INSERT INTO tblaccounts (username, password, usertype, userstatus, createdby, datecreated, timecreated) VALUES ('{$_POST['txtusername']}', '{$_POST['txtpassword']}', '{$_POST['cmbaccountType']}', '$status', '{$_SESSION['username']}', '$date', '$time')";
                $insert_result = mysqli_query($link, $sql);
                if ($insert_result) {
                    $date = date("Y-m-d");
                    $time = date("h:i:sa");
                    $action = "Create";
                    $module = "Accounts Management";
                    $sql = "INSERT INTO tbllogs (datelog, timelog, action, module, id, performedby) VALUES ('$date', '$time', '$action', '$module', '{$_POST['txtusername']}', '{$_SESSION['username']}')";
                    $log_result = mysqli_query($link, $sql);
                    if ($log_result) {
                        $success = "<strong><span style='color:green;'>You have successfully added a new account</span></strong>";
                    } else {
                        $errors[] = "<font color='red'>Error on Insert Log Statement</font>";
                    }
                } else {
                    $errors[] = "<strong><span style='color: red;'>Error on adding new account</span></strong>";
                }
            } else {
                $errors[] = "<strong><span style='color: red;'>Username is already in use</span></strong>";
            }
        } else {
            $errors[] = "<strong><span style='color: red;'>Error on finding if user exists</span></strong>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Account - Celestial Scholar University Student Portal</title>
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

<div class="container">
    <div class="createaccform">
        <p>Fill up this form and submit in order to add a new user</p>
        <form id="createaccount" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="txtusername"><strong>Username:</strong></label>
            <input type="text" id="txtusername" name="txtusername" required>

            <label for="txtpassword"><strong>Password:</strong></label>
            <input type="password" id="txtpassword" name="txtpassword" required>

            <div id="showPasswordContainer">
                <input type="checkbox" id="showPassword">
                <label class="toggle-switch" for="showPassword"></label>
                <label for="showPassword" id="passwordText">Show Password</label>
            </div>

            <label for="cmbaccountType"><strong>Account Type:</strong></label>
            <select name="cmbaccountType" id="cmbaccountType" required>
                <option value="">--Select User Type--</option>
                <option value="ADMINISTRATOR">Administrator</option>
                <option value="REGISTRAR">Registrar</option>
                <option value="STAFF">Staff</option>
            </select>

            <input type="submit" name="btnsubmit" class="btnsubmit" value="Create New Account">
            <a href="accounts_management.php">Cancel</a>
        </form>
    </div>
</div>

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
            window.location.href = 'accounts_management.php';
        }, 1000);"; 
    }
    ?>
</script>

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
        var form = document.getElementById('createaccount');
        var phpErrors = form.querySelector('.php-errors');
        if (form.checkValidity()) {
            btn.value = "Creating please wait a moment...";
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.7';
            
            setTimeout(function() {
                btn.value = "Create New Account";
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

    document.getElementById("showPassword").addEventListener("change", function() {
        var passwordField = document.getElementById("txtpassword");
        var passwordText = document.getElementById("passwordText");
        if (this.checked) {
            passwordField.type = "text";
            passwordText.textContent = "Hide Password";
        } else {
            passwordField.type = "password";
            passwordText.textContent = "Show Password";
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        var passwordField = document.getElementById("txtpassword");
        passwordField.type = "password";
        var passwordText = document.getElementById("passwordText");
        passwordText.textContent = "Show Password";
    });
</script>
</body>
</html>
