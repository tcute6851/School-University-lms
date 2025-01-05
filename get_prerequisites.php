<?php
require_once "config.php";

if (isset($_GET['course'])) {
    $course = $_GET['course'];
    
    $options = "<option value=''>--Select Prerequisite--</option>";
    
    $sql = "SELECT subjectcode FROM tblsubject WHERE course = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $course);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $subjectcode);
    
    while (mysqli_stmt_fetch($stmt)) {
        $options .= "<option value='{$subjectcode}'>{$subjectcode}</option>";
    }
    
    mysqli_stmt_close($stmt);
    
    echo $options;
} else {
    echo "<option value=''>--Select Course First--</option>";
}
?>
