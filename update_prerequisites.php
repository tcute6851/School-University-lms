<?php
require_once "config.php";

$course = $_GET['course'];

$response = "<option value='None'>NO PREREQUISITE</option>";

if (!empty($course)) {
    $sql = "SELECT DISTINCT prerequisite FROM (
        SELECT prerequisite1 AS prerequisite FROM tblsubject WHERE course = ?
        UNION
        SELECT prerequisite2 AS prerequisite FROM tblsubject WHERE course = ?
        UNION
        SELECT prerequisite3 AS prerequisite FROM tblsubject WHERE course = ?
    ) AS unique_prerequisites WHERE prerequisite IS NOT NULL AND prerequisite != ''";

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $course, $course, $course);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $prerequisite);

    while (mysqli_stmt_fetch($stmt)) {
        if ($prerequisite !== "None") {
            $response .= "<option value='$prerequisite'>$prerequisite</option>";
        }
    }

    mysqli_stmt_close($stmt);
}

echo $response;
?>
