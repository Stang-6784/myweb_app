<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_files WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$file = mysqli_fetch_assoc($result);

if ($file) {
    $path = "files/" . $file["files_name"];

    if (file_exists($path)) {
        unlink($path);
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM tbl_files WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);
}

header("Location: file.php");
exit;
