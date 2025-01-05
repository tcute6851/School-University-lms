<?php
$errors = array(); // Initialize an array to store errors
$login_message = ""; // Initialize login message variable
// Check if the form is submitted
if(isset($_POST['btnlogin'])){
    // Check if the username and password fields are empty
    if(empty($_POST['txtusername']) || empty($_POST['txtpassword'])) {
        $errors[] = "Please enter both username and password.";
    } else {
        sleep(1);
        // Require the config file
        require_once "config.php";
        // Build the template for the login SQL statement
        $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND userstatus = 'ACTIVE'";
        // Check if the SQL statement will run on the link by preparing the statement
        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind the data from the login form to the SQL statement
            mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);
            // Check if the statement will execute
            if(mysqli_stmt_execute($stmt)) {
                // Get the result of executing the statement
                $result = mysqli_stmt_get_result($stmt);
                // Check if there is a result
                if(mysqli_num_rows($result) > 0) {
                    // Fetch the result into an array
                    $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    // Create a session
                    session_start();
                    // Record session
                    $_SESSION['username'] = $_POST['txtusername'];
                    $_SESSION['usertype'] = $account['usertype'];
                    // Redirect to the accounts page
                    header("location:index.php");
                } else {
                    $errors[] = "<strong><span style='color: red;'>Incorrect login details or account is disabled/inactive</span></strong>";
                }
            } else {
                $errors[] = "Error on the login statement";
            }
        }
    }

    // Check if login is successful before displaying the form
    if (!empty($errors)) {
        $login_message = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - Celestial Scholar University</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
<header class="header">
    <div class="header-container">
    <div class="logo">
    <img src="./img/logo1.svg" alt="Website Logo">
</div>

        <h1 class="brand">Celestial Scholar University Student Portal</h1>
    </div>
</header>

    <div class="login-container">
        <div class="background"></div>
        <div class="login-box">
            <div class="colorful-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            <div class="overlay"></div>
            <div class="inner-overlay"></div>
            <div class="glow"></div>
            <div class="glow-2"></div>
            <h2>Login</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="login-form">
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
            }, 3000);
        }

        <?php
        if (!empty($login_message)) {
            echo "displayNotification('<b>" . addslashes($login_message) . "</b>', 'error');";
        }
        ?>
    </script>
            <h2>ENTER USERNAME:</h2>
                <div class="input-field">
                    <input type="text" id="txtusername" name="txtusername" required>
                    <label for="txtusername">Username</label>
                    <div class="bar"></div>
                </div>
                <h2>ENTER PASSWORD:</h2>
                <div class="input-field">
                    <input type="password" id="txtpassword" name="txtpassword" required>
                    <label for="txtpassword">Password</label>
                    <div class="bar"></div>
                </div><br><br>
                <div id="showPasswordContainer">
                    <input type="checkbox" id="showPassword">
                    <label class="toggle-switch" for="showPassword"></label>
                    <label style="color:black;font-weight:bolder;" for="showPassword" id="passwordText">Show Password</label>
                </div><br><br>
                <input type="submit" class="btnlogin" name="btnlogin" value="Login">
                <div class="animated-squares">
                    <div class="square"></div>
                    <div class="square"></div>
                    <div class="square"></div>
                    <div class="square"></div>
                </div>
                <div class="colorful-circles">
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                </div>
                <div class="rotating-triangles">
                    <div class="triangle"></div>
                    <div class="triangle"></div>
                    <div class="triangle"></div>
                    <div class="triangle"></div>
                </div>
            </form>
            <div class="php-errors"></div>
        </div>
    </div>
    <script>
        document.querySelector('.btnlogin').addEventListener('click', function(event) {
            var btn = event.target;
            var form = document.getElementById('login-form');
            var phpErrors = form.querySelector('.php-errors'); 
            if (form.checkValidity()) {
                btn.value = "please wait Logging in...";
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.7';
                
                setTimeout(function() {
                    btn.value = "Login";
                    btn.style.pointerEvents = 'auto';
                    btn.style.opacity = '1';
                    
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
    <footer class="footer">
    <div class="footer-container">
        <div class="footer-menu">
            <ul>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
        <div class="social-media">
            <a href="#" target="_blank">
                <img src="img/facebook.svg" alt="Facebook">
            </a>
            <a href="#" target="_blank">
                <img src="img/twitter.svg" alt="Twitter">
            </a>
            <a href="" target="_blank">
                <img src="img/instagram.svg" alt="Instagram">
            </a>
        </div>
    </div>
    <div class="legal-info">
        <p>&copy; 2024 Celestial Scholar University. All rights reserved.</p>
        <p><a href="#">Terms of Service</a> | <a href="#">Privacy Policy</a></p>
    </div>
</footer>
</body>

</html>
