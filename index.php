<?php
require_once "config.php";
require_once "session-checker.php";
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celestial Scholar University Student Portal</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <style>
        /* Base Styles */
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

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header and Navigation */
        header {
            background-color: #003366;
            padding: 15px 0;
        }

        nav.topnav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        nav.topnav a {
            color: #ffffff;
            margin: 0 15px;
            font-weight: bold;
        }

        nav.topnav a:hover {
            background-color: #00509e;
            color:  #01112c;
            text-decoration: underline;
        }

        nav .brand {
            font-size: 24px;
            color: #ffffff;
        }

        nav .dropdown {
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
            color:  #01112c;
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
        .hero {
            background-color:  #01112c;
            color: #ffffff;
            text-align: center;
            padding: 80px 20px;
        }

        .hero-heading {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .hero-text {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .btn {
            background-color: #00796b;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #004d40;
        }

        /* Features Section */
        .features {
            background-color: #f9f9f9;
            padding: 60px 0;
        }

        .features-heading {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 40px;
            color: #333;
        }

        .feature-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .feature {
            flex: 1 1 calc(33.333% - 20px);
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }

        .feature:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-heading {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .feature-text {
            font-size: 1rem;
            color: #666;
        }

        /* Footer Section */
        .footer {
            background-color: #003366;
            color: #ffffff;
            padding: 40px 0;
            text-align: center;
        }

        .footer .social-media {
            margin-bottom: 20px;
        }

        .footer .social-media a {
            margin: 0 10px;
        }

        .footer .social-media img {
            width: 24px;
        }

        .footer ul {
            list-style-type: none;
            padding: 0;
        }

        .footer ul li {
            display: inline;
            margin: 0 10px;
        }

        .footer ul li a {
            color: #ffffff;
        }

        .footer ul li a:hover {
            text-decoration: underline;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            nav.topnav {
                flex-direction: column;
                align-items: flex-start;
            }
    .brand {
        font-size: 18px;
    }

    .dropdown-content {
        width: 60%;
        right: 0;
        top: 50px;
    }
    .dropdown-content a {
            color:  #01112c;
            padding: 10px;
            text-align: center;
            display: block;
        }

            .brand {
                margin-bottom: 20px;
            }

            .feature-row {
                flex-direction: column;
            }

            .feature {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .hero-heading {
                font-size: 1.8rem;
            }

            .hero-text {
                font-size: 1rem;
            }

            .btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .features-heading {
                font-size: 1.5rem;
            }

            .feature-heading {
                font-size: 1.2rem;
            }

            nav .profile-picture {
                width: 35px;
                height: 35px;
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
           <p> username: <?php echo $_SESSION['username']; ?></p>
           <?php if ($Usertype === 'ADMINISTRATOR'||$Usertype === 'REGISTRAR' || $Usertype === 'STAFF'): ?><a href="portalmanagement.php">Portal</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<div class="hero">
    <div class="container">
        <div class="hero-content">
            <h2 class="hero-heading">WELCOME TO Celestial Scholar University</h2>
            <p class="hero-text">WEB DEVELOPER: ARVINNE P. TORNO</p>
            <p class="hero-text">DATA ADMINISTRATOR: ARVINNE P. TORNO</p>
            <a href="#" class="btn">Learn More</a>
        </div>
    </div>
</div>

<div class="features">
    <div class="container">
        <h2 class="features-heading">Features</h2>
        <div class="feature-row">
            <div class="feature">
                <h3 class="feature-heading">Easy Access</h3>
                <p class="feature-text">Quick access to your grades, subjects, and more from anywhere.</p>
            </div>
            <div class="feature">
                <h3 class="feature-heading">Secure Login</h3>
                <p class="feature-text">Your data is safe with our multi-layered security protocols.</p>
            </div>
            <div class="feature">
                <h3 class="feature-heading">Seamless Experience</h3>
                <p class="feature-text">Navigate effortlessly with our intuitive interface.</p>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <div class="container">
        <div class="social-media">
            <a href="#"><img src="./img/facebook.svg" alt="Facebook"></a>
            <a href="#"><img src="./img/instagram.svg" alt="Instagram"></a>
            <a href="#"><img src="./img/twitter.svg" alt="Twitter"></a>
        </div>
        <ul>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms & Conditions</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </div>
</div>
</body>
</html>
