<?php
$message = "";
require_once "config.php";
require_once "session-checker.php";

// Fetch user data
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Connect to the database
$link = mysqli_connect("localhost", "root", "", "stdp");
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$studentnumber = '';
if (isset($_GET['studentnumber'])) {
    $studentnumber = $_GET['studentnumber']; 
}
if (isset($_POST['btnsearch'])) {
    $studentnumber = $_POST['studentnumber']; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celestial Scholar University Student Portal Management</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@500&display=swap">
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
            <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
            <div class="dropdown-content">
                <p> Username: <?= htmlspecialchars($_SESSION['username']); ?></p>
                <a href="portalmanagement.php">Portal</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>

<br><br>
<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <label for="studentnumber"><strong>Student Number:</strong></label>
    <input type="number" id="studentnumber" name="studentnumber" value="<?= htmlspecialchars($studentnumber ?? ''); ?>">
    <input type="submit" style="background-color: orange; color: #fff; font-weight: bold" name="btnsearch" value="Search"><br><br>
    <button type="button" style="width: 180px; height: 30px; margin-right: 10px; background-color: orange; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight:bold" onclick="openLogsModal()">View Logs</button>
</form>

<div class="modallogs" id="logsModal">
    <div class="modal-contentlogs">
        <h2><strong>Logs</strong></h2>
        <strong>Search:</strong> <input type="text" id="txtlogsearch">
        <button type="button" id="btnLogSearch" style="width: 120px; height: 30px; margin-right:5px;">Search Logs</button>
        <input type="file" id="fileInput" style="display: none;" accept=".xls">
        <label for="fileInput" id="fileInputLabel" style="cursor: pointer; width: 180px; height: 30px; font-weight:bold;color:black; background-color:lightblue; margin-left:20px;">Import As Excel</label>
        <div id="logsContent"></div> 
        <button type="button" onclick="closeLogsModal()" style="font-weight: bold;">Close</button>
        <button type="button" style="width: 120px; height: 50px;background-color:Red;color:white; margin-left:5px;" onclick="openDeleteAllLogsModal()">Delete All Logs</button>
        <div class="modal" id="deleteAllLogsModal">
            <div class="modal-content">
                <h2><strong>Are you sure you want to delete all logs?</strong></h2>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="deleteAllLogsForm" method="POST" onsubmit="return confirmDeleteLogs()">
                    <input type="submit" name="btnDeleteLogs" value="Yes" style="font-weight: bold;background-color:green;">
                    <button type="button" onclick="closeDeleteAllLogsModal();" style="font-weight: bold;background-color:red;">No</button>
                </form>
            </div>
        </div>
    </div>   
</div>

<script>
    window.onclick = function(event) {
        var modal = document.querySelector(".modal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function openDeleteAllLogsModal() {
        var modal = document.getElementById('deleteAllLogsModal');
        modal.style.display = 'block';
    }

    function closeDeleteAllLogsModal() {
        var modal = document.getElementById('deleteAllLogsModal');
        modal.style.display = 'none';
    }

    function toggleDropdown() {
        var dropdownContent = document.getElementById("dropdownContent");
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    }

    function openLogsModal() {
        var modal = document.getElementById('logsModal');
        modal.style.display = 'block';
        fetchLogs();
    }

    function closeLogsModal() {
        var modal = document.getElementById('logsModal');
        modal.style.display = 'none';
    }

    function fetchLogs() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_logs.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('logsContent').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
</script>

<?php
if (!empty($studentnumber)) {
    echo '<div class="student-info" style="text-align: center;">
        <h2>Student Information</h2>
        <p><strong>Student Number: ' . htmlspecialchars($studentnumber) . '</strong></p>';

    // Fetch student information
    $sql_student_info = "SELECT studentnumber, lastname, firstname, middlename, course, yearlevel FROM tblstudent WHERE studentnumber = ?";
    $stmt_student_info = mysqli_prepare($link, $sql_student_info);
    mysqli_stmt_bind_param($stmt_student_info, "s", $studentnumber);
    mysqli_stmt_execute($stmt_student_info);
    mysqli_stmt_bind_result($stmt_student_info, $studentnumber, $lastname, $firstname, $middlename, $course, $yearlevel);
    mysqli_stmt_fetch($stmt_student_info);

    echo '<p><strong>Name: ' . (isset($lastname) ? htmlspecialchars($lastname) . ' ' . htmlspecialchars($firstname) . ' ' . htmlspecialchars($middlename) : '') . '</strong></p>
        <p><strong>Course: ' . (isset($course) ? htmlspecialchars($course) : '') . '</strong></p>
        <p><strong>Year Level: ' . (isset($yearlevel) ? htmlspecialchars($yearlevel) : '') . '</strong></p>';

    mysqli_stmt_close($stmt_student_info);
    echo '<div class="main">';

    // Fetch subjects
    $sql_subjects = "SELECT s.subjectcode, s.description, s.unit, 
                     CONCAT_WS(' ',
                        IFNULL(s.prerequisite1, ''), 
                        IFNULL(s.prerequisite2, ''), 
                        IFNULL(s.prerequisite3, '')
                     ) AS prerequisites
                     FROM tblsubject s
                     LEFT JOIN tblgrade g ON s.subjectcode = g.subjectcode AND g.studentnumber = ?
                     LEFT JOIN tblgrade g1 ON s.prerequisite1 = g1.subjectcode AND g1.studentnumber = ?
                     LEFT JOIN tblgrade g2 ON s.prerequisite2 = g2.subjectcode AND g2.studentnumber = ?
                     LEFT JOIN tblgrade g3 ON s.prerequisite3 = g3.subjectcode AND g3.studentnumber = ?
                     WHERE s.course = ? 
                     AND g.studentnumber IS NULL 
                     AND (s.prerequisite1 IS NULL OR g1.studentnumber IS NOT NULL) 
                     AND (s.prerequisite2 IS NULL OR g2.studentnumber IS NOT NULL) 
                     AND (s.prerequisite3 IS NULL OR g3.studentnumber IS NOT NULL)";

    $stmt_subjects = mysqli_prepare($link, $sql_subjects);
    mysqli_stmt_bind_param($stmt_subjects, "sssss", $studentnumber, $studentnumber, $studentnumber, $studentnumber, $course);
    mysqli_stmt_execute($stmt_subjects);
    mysqli_stmt_bind_result($stmt_subjects, $subjectcode, $description, $unit, $prerequisites);

    $row_count = 0;
    echo "<table>";
    echo "<tr>";
    echo "<th>Subject Code</th>";
    echo "<th>Description</th>";
    echo "<th>Unit</th>";
    echo "<th>Pre-requisites</th>";
    echo "</tr>";

    while (mysqli_stmt_fetch($stmt_subjects)) {
        $row_count++;
        echo "<tr>";
        echo "<td>" . htmlspecialchars($subjectcode) . "</td>";
        echo "<td>" . htmlspecialchars($description) . "</td>";
        echo "<td>" . htmlspecialchars($unit) . "</td>";
        echo "<td>" . htmlspecialchars($prerequisites) . "</td>";
        echo "</tr>";
    }

    if ($row_count === 0) {
        echo "<tr><td colspan='4'>No subjects found.</td></tr>";
    }

    echo "</table>";

    mysqli_stmt_close($stmt_subjects);
    echo '</div>';
}
?>
</body>
<br><br><br><br><br><br><br><br><br><br>
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
</html>

<?php
mysqli_close($link);
?>
