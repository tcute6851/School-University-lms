<?php
$message = "";
require_once "config.php";
session_start();
$link = mysqli_connect("localhost", "root", "", "stdp");
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$Username = $_SESSION['username'];
// Retrieve user profile information
$proquery = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmtprof = mysqli_prepare($link, $proquery);
mysqli_stmt_bind_param($stmtprof, "s", $Username);
mysqli_stmt_execute($stmtprof);
mysqli_stmt_bind_result($stmtprof, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmtprof);
mysqli_stmt_close($stmtprof);
// Retrieve student information
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
$studentnumber = $_SESSION['username'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View subject to be taken - Celestial Scholar University Student Portal</title>
    <link rel="stylesheet" href="view.css">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
</head>
<d>
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
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="viewgrade.php">View Grades</a>
            <a href="viewsubject.php">Subjects available</a>
        <?php endif; ?>
        <div class="profile-picture-container">
            <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-picture">
            <div class="dropdown-content">
                <p> username: <?php echo $_SESSION['username']; ?></p>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>

<div class="student-info" style="text-align: center;">
    <h2>Students Information</h2>
    <p><strong>Student Number: <?php echo $studentnumber; ?></strong></p>
    <button onclick="exportToPDF()">Download as PDF</button>
    <?php
    $sql_student_info = "SELECT studentnumber, lastname, firstname, middlename, course, yearlevel FROM tblstudent WHERE studentnumber = ?";
    if ($stmt = mysqli_prepare($link, $sql_student_info)) {
        mysqli_stmt_bind_param($stmt, "s", $studentnumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $studentnumber, $lastname, $firstname, $middlename, $course, $yearlevel);
        mysqli_stmt_fetch($stmt);
        echo '<p><strong>Name: ' . $lastname . ' ' . $firstname . ' ' . $middlename . '</strong></p>';
        echo '<p><strong>Course: ' . $course . '</strong></p>';
        echo '<p><strong>Year Level: ' . $yearlevel . '</strong></p>';
        mysqli_stmt_close($stmt);
    }
    ?>
</div>

<div class="main">
    <h2>Available Subjects</h2>
    <?php
   $sql_select_subjects = "
   SELECT subjectcode, description, unit, course
   FROM tblsubject
   WHERE course = ? 
   AND (
       (prerequisite1 IS NULL AND prerequisite2 IS NULL AND prerequisite3 IS NULL)
       AND (
           prerequisite1 IS NOT NULL AND prerequisite2 IS NOT NULL AND prerequisite3 IS NOT NULL AND
           EXISTS (
               SELECT 1 FROM tblgrade WHERE studentnumber = ? AND subjectcode IN (prerequisite1, prerequisite2, prerequisite3)
           )
       )
       AND (
           prerequisite1 IS NOT NULL AND prerequisite2 IS NOT NULL AND prerequisite3 IS NULL AND
           EXISTS (
               SELECT 1 FROM tblgrade WHERE studentnumber = ? AND subjectcode IN (prerequisite1, prerequisite2)
           )
       )
       AND (
           prerequisite1 IS NOT NULL AND prerequisite2 IS NULL AND prerequisite3 IS NULL AND
           NOT EXISTS (
               SELECT 1 FROM tblgrade WHERE studentnumber = ? AND subjectcode = prerequisite1
           )
       )
       AND (
           prerequisite1 IS NULL AND prerequisite2 IS NOT NULL AND prerequisite3 IS NOT NULL AND
           NOT EXISTS (
               SELECT 1 FROM tblgrade WHERE studentnumber = ? AND subjectcode IN (prerequisite2, prerequisite3)
           )
       )
       AND (
           prerequisite1 IS NULL AND prerequisite2 IS NOT NULL AND prerequisite3 IS NULL AND
           NOT EXISTS (
               SELECT 1 FROM tblgrade WHERE studentnumber = ? AND subjectcode = prerequisite2
           )
       )
       AND (
           prerequisite1 IS NULL AND prerequisite2 IS NULL AND prerequisite3 IS NOT NULL AND
           NOT EXISTS (
               SELECT 1 FROM tblgrade WHERE studentnumber = ? AND subjectcode = prerequisite3
           )
       )
   )
   UNION
   SELECT subjectcode, description, unit, course
   FROM tblsubject
   WHERE course = ?
   AND NOT EXISTS (
       SELECT subjectcode FROM tblgrade WHERE studentnumber = ? AND subjectcode = tblsubject.subjectcode
   )
   ";

$stmt_subjects = mysqli_prepare($link, $sql_select_subjects);
mysqli_stmt_bind_param($stmt_subjects, "sssssssss", $course, $studentnumber, $studentnumber, $studentnumber, $studentnumber, $studentnumber, $studentnumber, $course, $studentnumber);
if ($stmt_subjects) {
mysqli_stmt_execute($stmt_subjects);
mysqli_stmt_bind_result($stmt_subjects, $subjectcode, $description, $unit, $course);

echo "<table>";
echo "<tr>";
echo "<th>Subject Code</th>";
echo "<th>Description</th>";
echo "<th>Unit</th>";
echo "<th>Course</th>";
echo "</tr>";

while (mysqli_stmt_fetch($stmt_subjects)) {
   echo "<tr>";
   echo "<td><strong>$subjectcode</strong></td>";
   echo "<td><strong>$description</strong></td>";
   echo "<td><strong>$unit</strong></td>";
   echo "<td><strong>$course</strong></td>";
   echo "</tr>";
}

echo "</table>";

// Check if any subjects were found
if (mysqli_stmt_num_rows($stmt_subjects) === 0) {
   echo "<p>No new available subjects found.</p>";
}

mysqli_stmt_close($stmt_subjects);
} else {
echo "Error: " . mysqli_error($link);
}
?>
</div>
<script>
    function exportToPDF() {
        var { jsPDF } = window.jspdf;
        var doc = new jsPDF();

        // Add student information as text
        doc.setFontSize(12);
        doc.text(`Student Information`, 10, 10);
        doc.text(`Student Number: <?= $studentnumber; ?>`, 10, 20);
        doc.text(`Name: <?= $lastname . ' ' . $firstname . ' ' . $middlename; ?>`, 10, 30);
        doc.text(`Course: <?= $course; ?>`, 10, 40);
        doc.text(`Year Level: <?= $yearlevel; ?>`, 10, 50);

        // Prepare the table data for the subjects
        var tableData = [];
        var rows = document.querySelectorAll('.main table tr');
        rows.forEach(function(row, index) {
            var cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                var subjectCode = cells[0].textContent.trim();
                var description = cells[1].textContent.trim();
                var unit = cells[2].textContent.trim();
                var course = cells[3].textContent.trim();
                tableData.push([subjectCode, description, unit, course]);
            }
        });

        // Define the columns and add the table using autoTable
        doc.autoTable({
            startY: 60, // start position of the table below student info
            head: [['Subject Code', 'Description', 'Unit', 'Course']],
            body: tableData
        });

        // Save the PDF and trigger download
        doc.save('student-info.pdf');
    }
</script>

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
