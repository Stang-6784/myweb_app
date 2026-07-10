<?php
session_start();
// include 'navbar.php';
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM tbl_user WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
?>

<link rel="stylesheet" href="style.css">

<?php
include 'navbar.php';  
?>

<h2>Profile</h2>

<?php if (!empty($user["img"])): ?>
    <img src="avatars/<?= htmlspecialchars($user["img"]) ?>" class="profile-img">
<?php endif; ?>

<p>รหัสผู้ใช้: <?= $user["id"] ?></p>
<p>ชื่อ: <?= $user["name"] ?></p>
<p>อีเมล: <?= $user["email"] ?></p>
<?php if (!empty($user["bio"])): ?>
    <p>แนะนำตัว: <?= htmlspecialchars($user["bio"]) ?></p>
<?php endif; ?>
<?php if (!empty($user["address"])): ?>
    <p>ที่อยู่: <?= htmlspecialchars($user["address"]) ?></p>
<?php endif; ?>
<!-- <p>วันที่สมัคร: <?= $user["created_at"] ?></p> -->

<a href="profile_update.php" class="btn">แก้ไขโปรไฟล์</a>