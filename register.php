<?php
session_start();

require "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id FROM tbl_user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($exists) {
        $message = "อีเมลนี้ถูกใช้งานแล้ว";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO tbl_user (name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: login.php");
            exit;
        }

        $message = "สมัครสมาชิกไม่สำเร็จ: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    include 'navbar.php';
    ?>
    <div class="form-container">
        <h1>สมัครสมาชิก</h1>

    <form action="register.php" method="post" class="form">
        <label for="username" class="label">ชื่อผู้ใช้:</label>
        <input type="text" id="username" name="username" required class="input-field"><br><br>
        
        <label for="email" class="label">อีเมล:</label>
        <input type="email" id="email" name="email" required class="input-field"><br><br>

        <label for="password" class="label">รหัสผ่าน:</label>
        <input type="password" id="password" name="password" required class="input-field"><br><br>
        
        <input type="submit" value="สมัครสมาชิก" class="btn">
    </form>

    <p><?= $message ?></p>
    <a href="login.php">เข้าสู่ระบบ</a>
    </div>
</body>
</html>