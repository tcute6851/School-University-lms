<?php
// Start PHP block
$message = "";
require_once "config.php";
session_start();

// Database connection
$link = mysqli_connect("localhost", "root", "", "stdp");
if (!$link) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Get user information
$Username = $_SESSION['username'];
$proquery = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmtprof = mysqli_prepare($link, $proquery);
mysqli_stmt_bind_param($stmtprof, "s", $Username);
mysqli_stmt_execute($stmtprof);
mysqli_stmt_bind_result($stmtprof, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmtprof);
mysqli_stmt_close($stmtprof);

$Username = $_SESSION['username'];
$proquery = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmtprof = mysqli_prepare($link, $proquery);
mysqli_stmt_bind_param($stmtprof, "s", $Username);
mysqli_stmt_execute($stmtprof);
mysqli_stmt_bind_result($stmtprof, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmtprof);
mysqli_stmt_close($stmtprof);
$query = "SELECT a.usertype, a.username, s.lastname, s.firstname, s.middlename, s.course, s.yearlevel
          FROM tblaccounts a
          INNER JOIN tblstudent s ON a.username = s.studentnumber
          WHERE a.username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $Username, $lastname, $firstname, $middlename, $course, $yearlevel);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Grades - Celestial Scholar University Student Portal</title>
    <link rel="stylesheet" href="view.css">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
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
            <a href="portalmanagement.php">Portal</a>
        <?php elseif ($Usertype === 'REGISTRAR' || $Usertype === 'STAFF'): ?>
            <a href="index.php">Home</a>
            <a href="studentpage.php">Students</a>
            <a href="subject_management.php">Subjects</a>
            <a href="grades-management.php">Grades</a>
            <a href="portalmanagement.php">Portal</a>
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="viewgrade.php">View Grades</a>
            <a href="viewsubject.php">Subjects Available</a>
        <?php endif; ?>
        <div class="profile-picture-container">
            <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
            <div class="dropdown-content">
                <p>Username: <?= htmlspecialchars($_SESSION['username']) ?></p>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>

<div class="container">
    <?php if ($Usertype === 'STUDENT'): ?>
        <div class="student-info">
            <h2 class="info-heading">Student Information</h2>
            <div class="info-details">
                <p><strong>Student Number:</strong> <?= htmlspecialchars($Username) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($lastname) ?> <?= htmlspecialchars($firstname) ?> <?= htmlspecialchars($middlename) ?></p>
                <p><strong>Course:</strong> <?= htmlspecialchars($course) ?></p>
                <p><strong>Year Level:</strong> <?= htmlspecialchars($yearlevel) ?></p>
            </div>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="button" onclick="exportToPDF()" value="Download Grade as PDF">
        </form>
        <script>
        function exportToPDF() {
            var { jsPDF } = window.jspdf;
            var doc = new jsPDF();
            
            // Add student information as text
            doc.setFontSize(12);
            doc.text('Student Information', 10, 10);
            doc.text('Student Number: <?= htmlspecialchars($Username) ?>', 10, 20);
            doc.text('Name: <?= htmlspecialchars($lastname) ?> <?= htmlspecialchars($firstname) ?> <?= htmlspecialchars($middlename) ?>', 10, 30);
            doc.text('Course: <?= htmlspecialchars($course) ?>', 10, 40);
            doc.text('Year Level: <?= htmlspecialchars($yearlevel) ?>', 10, 50);
            
            // Prepare the table data for the grades
            var tableData = [];
            var rows = document.querySelectorAll('.Grade-table table tr');
            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    var subjectCode = cells[0].textContent.trim();
                    var description = cells[1].textContent.trim();
                    var unit = cells[2].textContent.trim();
                    var grade = cells[3].textContent.trim();
                    tableData.push([subjectCode, description, unit, grade]);
                }
            });
            
            // Add the table to the PDF
            doc.autoTable({
                startY: 60,
                head: [['Subject Code', 'Description', 'Unit', 'Grade']],
                body: tableData,
                margin: { top: 60 },
                theme: 'striped'
            });
            
            // Save the PDF and trigger download
            doc.save('Grades.pdf');
        }
        </script>
               </div>
        <div class="Grade-table">
            <h2>Taken Subjects and View Grades</h2>
            <?php
            echo '<div class="table-container">';
            echo '<table>';
            $sql_select_grades = "SELECT g.subjectcode, s.description, s.unit, g.grade, g.encodedby, g.dateencoded 
                                  FROM tblgrade g
                                  INNER JOIN tblsubject s ON g.subjectcode = s.subjectcode
                                  WHERE g.studentnumber = ?";
            if ($stmt_grades = mysqli_prepare($link, $sql_select_grades)) {
                mysqli_stmt_bind_param($stmt_grades, "s", $Username);
                mysqli_stmt_execute($stmt_grades);
                mysqli_stmt_bind_result($stmt_grades, $subjectcode, $description, $unit, $grade, $encodedby, $dateencoded);
                mysqli_stmt_store_result($stmt_grades);
                if (mysqli_stmt_num_rows($stmt_grades) > 0) {
                    echo "<tr><th>Subject Code</th><th>Description</th><th>Unit</th><th>Grade</th><th>Encoded By</th><th>Date Encoded</th></tr>";
                    while (mysqli_stmt_fetch($stmt_grades)) {
                        echo "<tr>
                                <td><strong>$subjectcode</strong></td>
                                <td><strong>$description</strong></td>
                                <td><strong>$unit</strong></td>
                                <td><strong>$grade</strong></td>
                                <td><strong>$encodedby</strong></td>
                                <td><strong>$dateencoded</strong></td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='color: red;'>No grades found.</p>";
                }
                mysqli_stmt_close($stmt_grades);
            } else {
                echo "<p>Error in preparing statement.</p>";
            }        
            ?>
                <?php else: ?>
        <p style="color: red;">Access Denied</p>
    <?php endif; ?>
        </div>

</div>
</body>
<br><br>
<footer class="footer">
    <div class="social-media">
        <a href="#"><img src="img/facebook.svg" alt="Facebook"></a>
        <a href="#"><img src="img/twitter.svg" alt="Twitter"></a>
        <a href="#"><img src="img/instagram.svg" alt="Instagram"></a>
    </div>
    <ul>
        <li><a style="color:white" href="#">Terms of Service</a></li>
        <li><a style="color:white" href="#">Privacy Policy</a></li>
    </ul>
</footer>
</html>

