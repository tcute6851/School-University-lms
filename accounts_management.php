<?php
$message = "";
require_once "config.php";
require_once "delete-account.php";
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
    $username = trim($_POST['txtusername']); 
    $studentnum = trim($_POST['txtusername']); 
    // Prepare delete statement for tblaccounts
    $sql_delete_account1 = "DELETE FROM tblaccounts WHERE username = ?";
    if($stmt_delete1 = mysqli_prepare($link, $sql_delete_account1)) {
        mysqli_stmt_bind_param($stmt_delete1, "s", $username);
        if(mysqli_stmt_execute($stmt_delete1)) {
            // Prepare delete statement for tblstudents
            $sql_delete_account2 = "DELETE FROM tblstudent WHERE studentnumber = ?";
            if($stmt_delete2 = mysqli_prepare($link, $sql_delete_account2)) {
                mysqli_stmt_bind_param($stmt_delete2, "s", $studentnum);
                if(mysqli_stmt_execute($stmt_delete2)) {
                    // Insert log for successful deletion
                    $sql_insert_log = "INSERT INTO tbllogs (datelog, timelog, id, performedby, action, module) VALUES (?, ?, ?, ?, ?, ?)";
                    if($stmt_log = mysqli_prepare($link, $sql_insert_log)) {
                        $date = date("Y-m-d");
                        $time = date("h:i:sa");
                        $module = "Accounts Management";
                        $action = "Delete";
                        mysqli_stmt_bind_param($stmt_log, "ssssss", $date, $time, $username, $_SESSION['username'], $action, $module);
                        if(mysqli_stmt_execute($stmt_log)) {
                            $message = "<strong><span style='color: green;'>You have successfully Deleted an account</span></strong>";
                        } else {
                            $message = "<strong><span style='color: red;'>Error on inserting logs</span></strong>";
                        }
                    } else {
                        $message = "<strong><span style='color: red;'>Error preparing log statement</span></strong>";
                    }
                } else {
                    $message = "<strong><span style='color: red;'>Error on deleting student account</span></strong>";
                }
            } else {
                $message = "<strong><span style='color: red;'>Error preparing delete statement for student account</span></strong>";
            }
        } else {
            $message = "<strong><span style='color: red;'>Error on deleting account</span></strong>";
        }
    } else {
        $message = "<strong><span style='color: red;'>Error preparing delete statement for account</span></strong>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management Page -  Celestial Scholar University Student Portal</title>
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
    <br><br>
    <main>
        <div class="notification" id="notification"></div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="txtsearch"><strong>Search:</strong></label>
            <input type="text" id="txtsearch" name="txtsearch">
            <input type="submit" class="button" name="btnsearch" value="Search"><br><br>
            <button type="button" class="button" onclick="openLogsModal()">View Logs</button>
            <strong><a href="create-account.php" class="button">Create new account</a></strong>
        </form>
        <div class="modal" id="deleteformmodal">
            <div class="modal-content"> 
            <h2>Account Deletion Confirmation</h2>
             <p>Are you sure you want to delete this account?</p> 
             <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"id="deleteForm" method="POST">
            <label for="deleteUsername" name="txtusername">Username:</label>
            <input type="text" id="deleteUsername" name="txtusername" readonly>
            <button type="submit" name="btnsubmit" value="Yes" onclick="confirmDeleteAccount()" style="font-weight: bold;background-color:green;">Yes</button>
            <button type="button" name="btncancel" onclick="closeDeleteModal();" style="font-weight: bold;background-color:red;">No</button>
                </form> </div> </div>
<div class="modallogs" id="logsModal">
    <div class="modal-contentlogs">
        <h2><strong>Logs</strong></h2>
        <strong>Search:</strong> <input type="text" id="txtlogsearch">
        <button type="button" id="btnLogSearch" style="width: 120px; height: 30px; margin-right:5px;">Search Logs</button>
        <input type="file" id="fileInput" style="display: none;" accept=".xls">
        <label for="fileInput" id="fileInputLabel" style="cursor: pointer; width: 180px; height: 30px; font-weight:bold;color:black; background-color:lightblue; margin-left:20px;">Export As Excel</label>
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
    document.getElementById("btnLogSearch").addEventListener("click", fetchLogs);
    document.getElementById("fileInputLabel").addEventListener("click", handleFileDownload);
    function fetchLogs() {
        var searchText = document.getElementById("txtlogsearch").value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_logs.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('logsContent').innerHTML = xhr.responseText;
            }
        };
        xhr.send('txtlogsearch=' + searchText);
    }
    function handleFileDownload() {
    var logsContent = document.getElementById("logsContent").innerHTML;
    var logsTable = "<table>" + logsContent + "</table>";
    var blob = new Blob([logsTable], { type: 'application/vnd.ms-excel' });
    var link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.download = 'Reportlogs.xls'; 
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

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
  <?php
echo '<div class="table-container">';
echo '<table>';
if (!function_exists('buildTable')) {
    function buildTable($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<thead>";
            echo "<tr>";
            echo "<th>Username</th> <th>Email</th> <th>Usertype</th> <th>Status</th> <th>Created by</th> <th>Date Created</th> <th>Action</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td data-label='Username'><strong>" . $row['username'] . "</strong></td>";
                echo "<td data-label='Email'><strong>" . $row['email'] . "</strong></td>";
                echo "<td data-label='Usertype'><strong>" . $row['usertype'] . "</strong></td>";
                echo "<td data-label='Status'><strong>" . $row['userstatus'] . "</strong></td>";
                echo "<td data-label='Created by'><strong>" . $row['createdby'] . "</strong></td>";
                echo "<td data-label='Date Created'><strong>" . $row['datecreated'] . "</strong></td>";
                echo "<td data-label='Action'>";
                echo "<a href='update-account.php?username=" . $row['username'] . "' class='btnupdate'><strong>Update</strong></a>&nbsp;"; 
                echo "<button onclick='openDeleteModal(\"" . $row['username'] . "\")' class='btndelete' style='width: 70px; height: 30px;'><strong>Delete</strong></button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "No record/s found.";
        }
    }
}

if(isset($_POST['btnsearch'])) {
    $sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username";
    if($stmt = mysqli_prepare($link, $sql)) {
        $searchvalue = '%' . $_POST['txtsearch'] . '%';
        mysqli_stmt_bind_param($stmt, "ss", $searchvalue, $searchvalue);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            buildTable($result);
        }
    } else {
        echo "Error on search";
    }
} else {
    $sql = "SELECT * FROM tblaccounts ORDER BY username";
    if ($stmt = mysqli_prepare($link, $sql)) {
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt); 
            buildTable($result);
        }
    } else {
        echo "Error on accounts load";
    }
}
mysqli_close($link);
echo '</div>';
?>
<br>

<footer class="footer">
    <div class="social-media">
        <a href="#"><img src="img/facebook.svg" alt="Facebook"></a>
        <a href="#"><img src="img/twitter.svg" alt="Twitter"></a>
        <a href="#"><img src="img/instagram.svg" alt="Instagram"></a>
    </div>
    <footer class="footer">
        <ul>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Privacy Policy</a></li>
        </ul>
    </footer>
</body>

</html>
