<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = mysqli_prepare($conn, "SELECT img FROM tbl_user WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && !empty($user["img"])) {
    $path = "avatars/" . $user["img"];

    if (file_exists($path)) {
        unlink($path);
    }

    $stmt = mysqli_prepare($conn, "UPDATE tbl_user SET img = '' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
}

header("Location: profile_update.php");
exit;
