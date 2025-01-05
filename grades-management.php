<?php
$message = "";
require_once "config.php";
require_once "subjectdelete.php";
$link = mysqli_connect("localhost", "root", "", "stdp");
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$Username = $_SESSION['username'];
$query = "SELECT usertype, profile_picture FROM tblaccounts WHERE username = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $Username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $Usertype, $profilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
if (isset($_POST['btnDeleteLogs'])) {
    $sql_delete_logs = "TRUNCATE TABLE tbllogs";
    if (mysqli_query($link, $sql_delete_logs)) {
        $message = "<strong><span style='color: green;'>You have Deleted all logs successfully</span></strong>";
        $date = date("Y-m-d");
        $time = date("h:i:sa");
        $action = "Deleted all logs";
        $id = $_SESSION['username'];
        $module = "Database Management";
        $insert_query = "INSERT INTO tbllogs (datelog, timelog, id, performedby, action, module) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt_log = mysqli_prepare($link, $insert_query)) {
            mysqli_stmt_bind_param($stmt_log, "ssssss", $date, $time, $id, $_SESSION['usertype'], $action, $module);
            if (mysqli_stmt_execute($stmt_log)) {
                $message .= "<br><strong><span style='color: green;'>Log for deletion inserted successfully</span></strong>";
            } else {
                $message .= "<br><strong><span style='color: red;'>Error inserting deletion log: " . mysqli_error($link) . "</span></strong>";
            }
        } else {
            $message .= "<br><strong><span style='color: red;'>Error preparing log statement: " . mysqli_error($link) . "</span></strong>";
        }        
    } else {
        $message = "<strong><span style='color: red;'>Error on deleting logs</span></strong>";
    }
}
if(isset($_POST['btnsubmit'])) {
    $subjectcode = trim($_POST['subjectcode']);
    $studentnumber = $_POST['studentnumber']; 
    $sqlgrade = "SELECT * FROM tblgrade WHERE subjectcode = ? AND studentnumber = ?";
    if($stmtgrade = mysqli_prepare($link, $sqlgrade)) {
        mysqli_stmt_bind_param($stmtgrade, "ss", $subjectcode, $studentnumber);
        if(mysqli_stmt_execute($stmtgrade)) {
            $resultgrade = mysqli_stmt_get_result($stmtgrade);
            if(mysqli_num_rows($resultgrade) > 0) {
                $sql_delete = "DELETE FROM tblgrade WHERE subjectcode = ? AND studentnumber = ?";
                if($stmt_delete = mysqli_prepare($link, $sql_delete)) {
                    mysqli_stmt_bind_param($stmt_delete, "ss", $subjectcode, $studentnumber);
                    if(mysqli_stmt_execute($stmt_delete)) {
                        $sql_log = "INSERT INTO tbllogs (datelog, timelog, action, module, id, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                        if($stmt_log = mysqli_prepare($link, $sql_log)){
                            $date = date("Y-m-d");
                            $time = date("h:i:sa");
                            $action = "Delete";
                            $module = "Grades Management";
                            $performedby = $Username;
                            mysqli_stmt_bind_param($stmt_log, "ssssss", $date, $time, $action, $module, $studentnumber, $performedby);
                            if(mysqli_stmt_execute($stmt_log)){
                                $message = "<strong><span style='color: green;'>You have been Successfully Deleted a Grade!</span></strong>";
                            } else {
                                $message = "<strong><span style='color: red;'>Error logging the action</span></strong>";
                            }
                        } else {
                            $message = "<strong><span style='color: red;'>Error preparing log statement</span></strong>";
                        }
                    } else {
                        $message = "Error deleting grade";
                    }
                } else {
                    $message = "<strong><span style='color: red;'>Error preparing delete statement</span></strong>";
                }
            }
        }
    }
}
$sql_insert = "INSERT INTO tblgrade (studentnumber, subjectcode, grade, encodedby, dateencoded)
SELECT DISTINCT tstd.studentnumber, ts.subjectcode, '', '', ''
FROM tblsubject ts
INNER JOIN tblstudent tstd ON ts.course = tstd.course
LEFT JOIN tblgrade tg ON ts.subjectcode = tg.subjectcode AND tstd.studentnumber = tg.studentnumber
WHERE NOT EXISTS (
    SELECT 1
    FROM tblgrade tg2
    WHERE tg2.studentnumber = tstd.studentnumber
    AND tg2.subjectcode = ts.subjectcode
)";

if(isset($_POST['btnsearch'])) {
    $studentnumber = $_POST['studentnumber'];
    $sql_student_info = "SELECT studentnumber, lastname, firstname, middlename, course, yearlevel FROM tblstudent WHERE studentnumber = ?";
    if($stmt = mysqli_prepare($link, $sql_student_info)) {
        mysqli_stmt_bind_param($stmt, "s", $studentnumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $studentnumber, $lastname, $firstname, $middlename, $course, $yearlevel);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
}

if(isset($_GET['studentnumber'])) {
    $studentnumber = $_GET['studentnumber'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades-management- Subject Advising System</title>
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo1.svg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@500&display=swap">
    </head>
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
                 <a href="portalmanagement.php">Portal</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>
    <main>
        <div class="notification" id="notification"></div>
        <br><br>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label for="studentnumber"><strong>Student Number:</strong></label>
        <input type="number" id="studentnumber" name="studentnumber">
        <input type="submit" style="background-color: orange; color: #fff; font-weight: bold"name="btnsearch" value="Search"><br><br>
        <button type="button" style="width: 180px; height: 30px; margin-right: 10px; background-color: orange; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight:bold" onclick="openLogsModal()">View Logs</button>
        <input type="submit"style="background-color: orange; color: #fff; font-weight: bold" onclick="exportToExcel()" value="Export Data to Excel"><br><br>
    <script>
        function exportToExcel() {
            var studentNumber = "<?php echo $studentnumber; ?>";
            var lastName = "<?php echo $lastname; ?>";
            var firstName = "<?php echo $firstname; ?>";
            var middleName = "<?php echo $middlename; ?>";
            var course = "<?php echo $course; ?>";
            var yearLevel = "<?php echo $yearlevel; ?>";
            var gradeRows = document.querySelectorAll('table tr');
            var html = "<table style='border-collapse: collapse; width: 100%; margin-top: 20px;'>";
            html += "<tr><th colspan='2' style='background-color: #007bff; color: white; padding: 10px;'>Student Information</th></tr>" +
                "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Student Number</td><td style='border: 1px solid #ddd; padding: 10px;'>" + studentNumber + "</td></tr>" +
                "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Name</td><td style='border: 1px solid #ddd; padding: 10px;'>" + lastName + '&nbsp;' + firstName + '&nbsp;' + middleName + "</td></tr>" +
                "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Course</td><td style='border: 1px solid #ddd; padding: 10px;'>" + course + "</td></tr>" +
                "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Year Level</td><td style='border: 1px solid #ddd; padding: 10px;'>" + yearLevel + "</td></tr>";

            html += "<tr><th colspan='4' style='background-color: #007bff; color: white; padding: 10px;'>Grades</th></tr>";
            html += "<tr><th style='border: 1px solid #ddd; padding: 10px;'>Subject Code</th><th style='border: 1px solid #ddd; padding: 10px;'>Description</th><th style='border: 1px solid #ddd; padding: 10px;'>Unit</th><th style='border: 1px solid #ddd; padding: 10px;'>Grade</th></tr>";
            gradeRows.forEach(function(row) {
                var cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    var subjectCode = cells[0].textContent.trim();
                    var description = cells[1].textContent.trim();
                    var unit = cells[2].textContent.trim();
                    var grade = cells[3].textContent.trim();
                    html += "<tr><td style='border: 1px solid #ddd; padding: 10px;'>" + subjectCode + "</td><td style='border: 1px solid #ddd; padding: 10px;'>" + description + "</td><td style='border: 1px solid #ddd; padding: 10px;'>" + unit + "</td><td style='border: 1px solid #ddd; padding: 10px;'>" + grade + "</td></tr>";
                }
            });

            html += "</table>";

            var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            if (window.navigator.msSaveBlob) {
                window.navigator.msSaveOrOpenBlob(blob, 'grade.xls');
            } else {
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'grade.xls';
                link.click();
            }
        }
    </script>
    </form>
    <?php
if(isset($studentnumber)) {
    echo '<div class="student-info" style="text-align: center;">
    <center>
        <h2>Students Information</h2>
        <p><strong>Student Number: ' . $studentnumber . '</strong></p>
        <p><strong>Name: ' . (isset($lastname) ? $lastname . '&nbsp;' . $firstname . '&nbsp;' . $middlename : '') . '</strong></p>
        <p><strong>Course: ' . (isset($course) ? $course : '') . '</strong></p>
        <p><strong>Year Level: ' . (isset($yearlevel) ? $yearlevel : '') . '</strong></p>
        <strong><a style="text-decoration:underline" href="addgrade.php?studentnumber=' . $studentnumber . '&lastname=' . (isset($lastname) ? $lastname : '') . '&firstname=' . (isset($firstname) ? $firstname : '') . '&middlename=' . (isset($middlename) ? $middlename : '') . '&course=' . (isset($course) ? $course : '') . '&yearlevel=' . (isset($yearlevel) ? $yearlevel : '') . '">Add Grades</a></strong>
    </center>
    </div>';
    echo '<div class="table-container">';
    echo '<table>';
    $sql_select_grades = "SELECT g.subjectcode, s.description, s.unit, g.grade, g.encodedby, g.dateencoded 
                          FROM tblgrade g
                          INNER JOIN tblsubject s ON g.subjectcode = s.subjectcode
                          WHERE g.studentnumber = ?";
    if($stmt_grades = mysqli_prepare($link, $sql_select_grades)) {
        mysqli_stmt_bind_param($stmt_grades, "s", $studentnumber);
        mysqli_stmt_execute($stmt_grades);
        mysqli_stmt_bind_result($stmt_grades, $subjectcode, $description, $unit, $grade, $encodedby, $dateencoded);
        $row_count = 0; 
        mysqli_stmt_store_result($stmt_grades); 
        $row_count = mysqli_stmt_num_rows($stmt_grades);
        if($row_count > 0) {
            echo "<table>
                    <tr><th>Subject Code</th><th>Description</th><th>Unit</th><th>Grade</th><th>Encoded By</th><th>Date Encoded</th><th>Action</th></tr>";
            while(mysqli_stmt_fetch($stmt_grades)) {
                echo "<tr>
                        <td><strong>". $subjectcode. "</strong></td>
                        <td><strong>".$description."</strong></td>
                        <td><strong>".$unit."</strong></td>
                        <td><strong>".$grade."</strong></td>
                        <td><strong>".$encodedby."</strong></td>
                        <td><strong>".$dateencoded."</strong></td>
                        <td>
                            <a href='updategrades.php?studentnumber=$studentnumber&subjectcode=$subjectcode' class='btnupdate'><strong>Update</strong></a><br><br>;
                            <button onclick='openDeleteModal(\"$subjectcode\")' class='btndelete'>Delete</button>

                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<span style='color:red;font-weight:bold;'>No records found</span>";
        }

        mysqli_stmt_close($stmt_grades);
    } else {
        echo "Error: " . mysqli_error($link);
    }
}

if(isset($_POST['btnsearch'])) {
    if(isset($_POST['txtsearch'])) { 
        $searchvalue = '%' . $_POST['txtsearch'] . '%';
        $sql_select_grades = "SELECT g.subjectcode, s.description, s.unit, g.grade, g.encodedby, g.dateencoded 
                              FROM tblgrade g
                              INNER JOIN tblsubject s ON g.subjectcode = s.subjectcode
                              WHERE g.studentnumber LIKE ?";
        if($stmt = mysqli_prepare($link, $sql_select_grades)) {
            mysqli_stmt_bind_param($stmt, "s", $searchvalue);
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
            }
        } else {
            echo "Error on search";
        }
    }
}
mysqli_close($link);
echo '</div>';
?>
    <div class="modal" id="deleteformmodal">
    <div class="modal-content">
        <h2><strong>Are you sure you want to delete this Subject </strong></h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="deleteForm" method="POST">
            <input type="text" id="deletesubject" name="subjectcode" readonly>
            <h2><strong> in this Student?</strong></h2>
            <input type="text" name="studentnumber" value="<?php echo $studentnumber; ?>"readonly>
            <button type="submit" name="btnsubmit" style="font-weight: bold;background-color:green;">Yes</button>
            <button type="button" name="btncancel" onclick="closeDeleteModal();" style="font-weight: bold;background-color:red;">No</button>
        </form>
    </div>
</div>
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
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="deleteAllLogsForm" method="POST" onsubmit="return confirmDeleteLogs()">
                    <input type="submit" name="btnDeleteLogs" value="Yes" style="font-weight: bold;background-color:green;">
                    <button type="button" onclick="closeDeleteAllLogsModal();" style="font-weight: bold;background-color:red;">No</button>
                </form>
            </div>
        </div>
    </div>   
</div>
    <script>
        function openDeleteModal(username) {
        var modal = document.getElementById("deleteformmodal");
        modal.style.display = "block";
        var deleteForm = document.getElementById("deleteForm");
        deleteForm.action = "accounts_management.php";
        var usernameInput = document.getElementById("deleteUsername");
        usernameInput.value = username;
    }
    function closeDeleteModal() {
        var modal = document.getElementById("deleteformmodal");
        modal.style.display = "none";
    }

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
<script>
            function openDeleteModal(subjectcode) {
            var modal = document.getElementById("deleteformmodal");
            modal.style.display = "block";
            var subjectcodeInput = document.getElementById("deletesubject");
            subjectcodeInput.value = subjectcode;
        }
        

    function closeDeleteModal() {
        var modal = document.getElementById('deleteformmodal');
        modal.style.display = 'none';
    }
    </script>
    <br><br><br><br><br><br><br><br><br><br>
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
</body>
</html>
