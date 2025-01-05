<?php
$errors = array();
$success = "";
$account = array();
require_once "config.php";
include "session-checker.php";
sleep(1);
if (isset($_POST['btnsubmit']) && isset($_GET['username'])) {
    $sql = "UPDATE tblaccounts SET password = ?, usertype = ?, userstatus = ? WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $_POST['txtpassword'], $_POST['cmbtype'], $_POST['rbstatus'], $_GET['username']);
        if (mysqli_stmt_execute($stmt)) {
            $sql_insert = "INSERT INTO tbllogs (datelog, timelog, action, module, id, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                $date = date("Y-m-d");
                $time = date("h:i:sa");
                $module = "Accounts Management";
                $action = "Update";
                $id = $_GET['username'];
                $performedby = isset($_SESSION['username']) ? $_SESSION['username'] : '';
                mysqli_stmt_bind_param($stmt_insert, "ssssss", $date, $time, $action, $module, $id, $performedby);
                if (mysqli_stmt_execute($stmt_insert)) {
                    $success = "<strong><span style='color:green;'>You have successfully updated an account</span></strong>";
                } else {
                    $errors[] = "<font color='red'>Error on Inserting Logs</font>";
                }
            }
        } else {
            $errors[] = "<font color='red'>Error on updating accounts</font>";
        }
    }
} else {
    if (isset($_GET['username']) && !empty(trim($_GET['username']))) {
        $sql_select = "SELECT * FROM tblaccounts WHERE username = ?";
        if ($stmt_select = mysqli_prepare($link, $sql_select)) {
            mysqli_stmt_bind_param($stmt_select, "s", $_GET['username']);
            if (mysqli_stmt_execute($stmt_select)) {
                $result = mysqli_stmt_get_result($stmt_select);
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                $errors[] = "<font color='red'>Error on loading current account data</font>";
            }
        }   
    } else {
        $errors[] = "<font color='red'>account is not found</font>";
    }
    $userstatus = isset($account['userstatus']) ? $account['userstatus'] : '';
}
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account - Celestial Scholar University Student Portal</title>
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
    <h2 class="form-title">Update Account</h2>
    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST" id="formupdate">
        <div class="formupdate">
        <strong>Username: <span><strong> <?php echo isset($account['username']) ? $account['username'] : ''; ?></strong></span><br><br></strong>
           <strong>  <label for="txtpassword">Password:</label></strong>
            <input type="password" id="txtpassword" name="txtpassword" value="<?php echo isset($account['password']) ? $account['password'] : ''; ?>" required></strong>
            <div id="showPasswordContainer">
                    <input type="checkbox" id="showPassword">
                    <label class="toggle-switch" for="showPassword"></label>
                    <label style="color:black;font-weight:bolder;" for="showPassword" id="passwordText">Show Password</label>
                </div><br>
        <div class="formupdate">
        <strong>Current User Type: </strong><span><strong> <?php echo isset($account['usertype']) ? $account['usertype'] : ''; ?></strong></span><br><br>
        <strong> <label for="cmbtype">Change user type to:</label></strong>
            <select name="cmbtype" id="cmbtype" required>
                <option value="">--Select User Type--</option>
                <option value="ADMINISTRATOR">Administrator</option>
                <option value="REGISTRAR">Registrar</option>
                <option value="STAFF">Student</option>
            </select>
        </div>
        <?php
        $userstatus = isset($account['userstatus']) ? $account['userstatus'] : '';
        if ($userstatus == 'ACTIVE') {
        ?>
        <div class="formupdate">
            <input type="radio" name="rbstatus" value="ACTIVE" checked>Active<br>
            <input type="radio" name="rbstatus" value="INACTIVE">Inactive<br>
        </div>
        <?php
        } else {
        ?>
        <div class="formupdate">
            <input type="radio" name="rbstatus" value="ACTIVE">Active<br>
            <input type="radio" name="rbstatus" value="INACTIVE" checked>Inactive<br>
        </div>
        <?php
        }        
        ?>
        <br>
        <div class="formupdate">
            <input type="submit" name="btnsubmit" value="Update" class="btn-submit">
            <a href="accounts_management.php" class="btn-cancel">Cancel</a>
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
