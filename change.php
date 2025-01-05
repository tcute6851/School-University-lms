<?php
require_once "config.php";
session_start();
sleep(1);
$Username = $_SESSION['username'];
$proquery = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmtprof = mysqli_prepare($link, $proquery);
mysqli_stmt_bind_param($stmtprof, "s", $Username);
mysqli_stmt_execute($stmtprof);
mysqli_stmt_bind_result($stmtprof, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmtprof);
mysqli_stmt_close($stmtprof);
$current_password = ""; 
$Username = $_SESSION['username'];
$current_password_query = "SELECT password FROM tblaccounts WHERE username = ?";
$stmt_current_password = mysqli_prepare($link, $current_password_query);
mysqli_stmt_bind_param($stmt_current_password, "s", $Username);
mysqli_stmt_execute($stmt_current_password);
mysqli_stmt_bind_result($stmt_current_password, $current_password);
mysqli_stmt_fetch($stmt_current_password);
mysqli_stmt_close($stmt_current_password);
$success = "";
$errors = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($new_password) || empty($confirm_password)) {
         $errors[] = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
         $errors[] = "Passwords do not match.";
    } else {
        $update_query = "UPDATE tblaccounts SET password = ? WHERE username = ?";
        $stmt_update = mysqli_prepare($link, $update_query);
        mysqli_stmt_bind_param($stmt_update, "ss", $new_password, $Username);
        if (mysqli_stmt_execute($stmt_update)) {
            $success = "<strong><span style='color:green;'>You have successfully updated a password</span></strong>";
            $sql_insert = "INSERT INTO tbllogs (datelog, timelog, action, module, id, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($link, $sql_insert);
            if ($stmt_insert) {
                $date = date("Y-m-d");
                $time = date("h:i:sa");
                $module = "View Grades";
                $action = "Change password";
                $id = $Username;
                $performedby = isset($_SESSION['username']) ? $_SESSION['username'] : '';
                mysqli_stmt_bind_param($stmt_insert, "ssssss", $date, $time, $action, $module, $id, $performedby);
                if (mysqli_stmt_execute($stmt_insert)) {
                } else {
                    $errors[] = "<font color='red'>Error on Inserting Logs: " . mysqli_error($link) . "</font>";
                }
                mysqli_stmt_close($stmt_insert);
            } else {
                $errors[] = "<font color='red'>Error on preparing log insert statement.</font>";
            }
        } else {
            $errors[] = "<font color='red'>Error updating password: " . mysqli_error($link) . "</font>";
        }
        mysqli_stmt_close($stmt_update);
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Celestial Scholar University</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link rel="stylesheet" href="view.css"> 
</head>
<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 20px;
    border-radius: 5px;
    display: none;
    z-index: 9999;
    background-color: #ffffffc9;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.8s ease-in-out forwards;
}

.container {
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}
.container h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}
.form-group {
    margin-bottom: 20px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #555;
}

input[type="password"] {
    width: calc(100% - 10px);
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.checkbox-group {
    margin-bottom: 20px;
    text-align: left;
}

.checkbox-group label {
    font-weight: normal;
    color: #666;
}

.btn-submit {
    padding: 10px 20px;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-submit:hover {
    background-color: #0056b3;
}
.show-password {
    margin-left: 5px;
    cursor: pointer;
}

@media (max-width: 480px) {
    .container {
        width: 90%;
    }
}
</style>
<header>
    <nav class="topnav" id="mytopnav">
        <?php if ($Usertype === 'ADMINISTRATOR'): ?>
            <a href="index.php">Home</a>
            <a href="accounts_management.php">Accounts</a>
            <a href="studentpage.php">Students</a>
            <a href="subject_management.php">Subjects</a>
            <a style="background-color: green" href="grades-management.php">Grades</a>
            <div class="welcome">
                <div class="logo"></div>
                <h3 style="margin-right:380px" class="brand"><strong>Celestial Scholar University Student Portal</strong></h3>
            </div>
            <div class="profile-picture-container">
                <div class="dropdown">
                    <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-picture dropbtn" onclick="toggleDropdown()"><span class="hamburger-icon">&#9776;</span>
                    <div class="dropdown-content" id="dropdownContent">
                         <a href="#"><?php echo $_SESSION['usertype']; ?></a>
                         <a href="advising-management.php">Advising Subject</a>
                        <a href="profile.php">Profile</a>
                        <a href="indexlogout.php">Logout</a>
                    </div>
                </div>
            </div>
            <div class="username"><?php echo $_SESSION['username']; ?></div>
        <?php elseif  ($Usertype === 'REGISTRAR'|| $Usertype === 'STAFF'): ?>
            <a href="index.php">Home</a>
            <a href="studentpage.php">Students</a>
            <a href="subject_management.php">Subjects</a>
            <a style="background-color: green" href="grades-management.php">Grades</a>
            <div class="welcome">
                <div class="reglogo"></div>
                <h3 style="margin-right:390px"class="brand"><strong>Celestial Scholar University Student Portal</strong></h3>
            </div>
            <div class="profile-picture-container">
                <div class="dropdown">
                    <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-picture dropbtn" onclick="toggleDropdown()"><span class="hamburger-icon">&#9776;</span>
                    <div class="dropdown-content" id="dropdownContent">
                         <a href="#"><?php echo $_SESSION['usertype']; ?></a>
                         <a href="advising-management.php">Advising Subject</a>
                        <a href="profile.php">Profile</a>
                        <a href="indexlogout.php">Logout</a>
                    </div>
                </div>
            </div>
            <div class="regusername"><?php echo $_SESSION['username']; ?></div>
        <?php else: ?>
            <a  href="index.php">Home</a>
            <a href="viewgrade.php">View Grades</a>
            <a href="subjects.php">Subjects to be taken</a>
            <div class="welcome">
                <div class="stulogo"></div>
                <h3 style="margin-right:500px;margin-top:10px"class="brand"><strong>Celestial Scholar University Student Portal</strong></h3>
            </div>
            <div class="stuprofile-picture-container">
                <div class="dropdown">
                    <img src="<?= $profilePicture ?>" alt="Profile Picture" class="stuprofile-picture dropbtn" onclick="toggleDropdown()"><span class="stuhamburger-icon">&#9776;</span>
                    <div class="dropdown-content" id="dropdownContent">
                        <a href="#"><?php echo $_SESSION['usertype']; ?></a>
                        <a href="profile.php">Profile</a>
                        <a style="background-color: green" href="change.php">Change Password</a>
                        <a href="indexlogout.php">Logout</a>
                    </div>
                </div>
            </div>
            <div class="stuusername"><?php echo $_SESSION['username']; ?></div>
        <?php endif; ?>
    </nav>
</header>
<body>
<div class="notification" id="notification"></div>
    <div class="container">
        <p>Current Password: <?php echo $current_password; ?></p>
        <h2>Change Password</h2>
        <form method="post" id="passform"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
            </div>
            <div class="form-group">
                <input type="checkbox" onclick="togglePassword()"> Show Password
            </div>
            <input type="submit" name="btnsubmit" value="Change Password" class="btn-submit"><br><br>
            <a href="viewgrade.php">Cancel</a>
        </form>
    </div>
<script>
    function togglePassword() {
        var passwordInput = document.getElementById("new_password");
        var confirmPasswordInput = document.getElementById("confirm_password");

        if (passwordInput.type === "password" && confirmPasswordInput.type === "password") {
            passwordInput.type = "text";
            confirmPasswordInput.type = "text";
        } else {
            passwordInput.type = "password";
            confirmPasswordInput.type = "password";
        }
    }
</script>
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
            window.location.href = 'viewgrade.php';
        }, 1000);"; 
    }
    ?>
        document.querySelector('.btn-submit').addEventListener('click', function(event) {
            var btn = event.target;
            var form = document.getElementById('passform');
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
            if (this.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        });
    </script>
<body>
<footer class="footer">
    <div class="social-media">
        <a href="https://www.facebook.com/ArellanoUniversityOfficial/"><img src="img/facebook.svg" alt="Facebook"></a>
        <a href="https://twitter.com/Arellano_U"><img src="img/twitter.svg" alt="Twitter"></a>
        <a href="https://www.instagram.com/explore/topics/495287895113599/arellano-university/"><img src="img/instagram.svg" alt="Instagram"></a>
    </div>
    <footer class="footer">
        <ul>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Privacy Policy</a></li>
        </ul>
    </footer>
</html>
