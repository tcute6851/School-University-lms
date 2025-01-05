<?php
require_once "config.php";
require_once "session-checker.php";
$message = ""; // Initialize message variable
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES['profile_picture']['name']);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
                $updateStmt = mysqli_prepare($link, "UPDATE tblaccounts SET profile_picture = ? WHERE username = ?");
                mysqli_stmt_bind_param($updateStmt, "ss", $targetFile, $Username);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
                mysqli_close($link);
                // After successfully updating the profile picture
                $message = "<font color='green'>Profile picture updated successfully.</font>";
            } else {
                $message = "<font color='red'>Error uploading file.</font>";
            }
        } else {
            $message = "<font color='red'>Invalid file type. Allowed types: </font>" . implode(", ", $allowedExtensions);
        }
    } else {
        $message = "<font color='red'>No file selected.</font>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <title>Edit Profile - Celestial Scholar University Student Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
           overflow:hidden;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 350px;
            text-align: center;
            margin-bottom: 20px;
            margin-top:20px
        }
        label {
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }
        input[type="file"] {
            display: none;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #1e87cd;
        }
        img {
            width: 100%;
            max-width: 200px;
            border-radius: 8px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        a {
            text-decoration: none;
        }
        button {
            background-color: #27ae60;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #219952;
        }
        .Welcome {
            background-color: #2848ff;
            padding: 10px;
            text-align: center;
            margin-bottom: 18px;
            width: 100%;
            color: #fff;
        }
        .logo {
            width: 50px;
            height: 50px;
            background-image: url('./img/logo1.svg');
            background-size: contain;
            background-repeat: no-repeat;
            border-radius: 50%;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
        }
        .brand {
            display: inline-block;
            vertical-align: middle;
            font-size: 20px;
        }
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #ffffff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .show {
            display: block;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #2848ff;
            text-align: center;
            font-size: 1rem;
            font-weight: bold;
        }

        .footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer ul li {
            display: inline-block;
            margin: 0 10px;
        }

        .social-media {
    margin-top: 20px;
    padding-top: 10px;
    background-color: #2848ff;
    text-align: center;
}

.social-media a {
    display: inline-block;
    margin: 0 10px;
}

.social-media img {
    width: 30px; 
    height: 30px; 
    border-radius: 50%;
}

    </style>
</head>
<body>
<div class="notification <?php echo !empty($message) ? 'show' : ''; ?>" id="notification"><?php echo $message; ?></div>
    <div class="Welcome">
        <div class="logo"></div>
        <h1 class="brand">Celestial Scholar University Student Portal</h1>
    </div>
<main>
    <form method="post" action="" enctype="multipart/form-data">
        <p style="color: black">NOTE: Kindly click the update to change your profile picture</p>
        <label for="profile_picture"><strong>Update Profile Picture</strong></label>
        <input type="file" name="profile_picture" id="profile_picture">
        <br>
        <input type="submit" name="update_profile" value="Save Profile">
    </form>
    <img src="<?php echo $profilePicture; ?>" alt="Current Profile Picture">
    <a href="index.php">
        <button>Back to Home</button>
    </a>
</main>
<script>
    // JavaScript code for displaying notifications and fading them out
    function displayNotification(message, type) {
        var notification = document.getElementById('notification');
        notification.classList.add('show');
        notification.innerHTML = '<div class="notification-content">' +
            '<div class="notification-icon ' + type + '"></div>' +
            '<div class="notification-message">' + message + '</div>' +
            '</div>';
        setTimeout(function () {
            notification.classList.remove('show');
        }, 4000); // 4 seconds
    }
    <?php
    if (!empty($message)) {
        echo "displayNotification('<b>" . addslashes($message) . "', 'error');";
    }
    ?>
</script>
</body>
<footer class="footer">
<div class="social-media">
    <a href="#"><img src="./img/facebook.svg" alt="Facebook"></a>
    <a href="#"><img src="./img/twitter.svg" alt="Twitter"></a>
    <a href="#"><img src="./img/instagram.svg" alt="Instagram"></a>
</div>

        <ul>
            <li><a style="color:white" href="#">Terms of Service</a></li>
            <li><a style="color:white" href="#">Privacy Policy</a></li>
        </ul>
    </footer>
</html>