<?php
require_once "config.php";
if(isset($_POST['subjectcode'])) {
    $subjectcode = $_POST['subjectcode'];
    $query = "SELECT description FROM tblsubject WHERE subjectcode = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "s", $subjectcode);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $description);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    echo $description;
} else {
    echo "";
}
?>
