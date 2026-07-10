<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_user WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $bio = trim($_POST["bio"]);
    $address = trim($_POST["address"]);
    $img_name = $user["img"];

    if (!empty($_FILES["img"]["name"])) {
        $file_tmp  = $_FILES["img"]["tmp_name"];
        $file_size = $_FILES["img"]["size"];
        $file_ext  = strtolower(pathinfo($_FILES["img"]["name"], PATHINFO_EXTENSION));

        $allowed = ["jpg", "jpeg", "png", "gif"];
        $image_info = @getimagesize($file_tmp);

        if (!in_array($file_ext, $allowed)) {
            $message = "รูปโปรไฟล์ต้องเป็น JPG, JPEG, PNG หรือ GIF";
        } elseif (!$image_info) {
            $message = "ไฟล์รูปภาพไม่ถูกต้อง";
        } else {
            $safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["img"]["name"]));
            $new_name = file_exists("avatars/" . $safe_name) ? uniqid() . "_" . $safe_name : $safe_name;
            $upload_path = "avatars/" . $new_name;

            $max_dim = 1024;
            [$src_w, $src_h] = $image_info;
            $ratio = min(1, $max_dim / $src_w, $max_dim / $src_h);
            $dst_w = (int) round($src_w * $ratio);
            $dst_h = (int) round($src_h * $ratio);

            $src_img = match ($file_ext) {
                "jpg", "jpeg" => imagecreatefromjpeg($file_tmp),
                "png" => imagecreatefrompng($file_tmp),
                "gif" => imagecreatefromgif($file_tmp),
            };

            $dst_img = imagecreatetruecolor($dst_w, $dst_h);

            if ($file_ext === "png" || $file_ext === "gif") {
                imagealphablending($dst_img, false);
                imagesavealpha($dst_img, true);
            }

            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

            $saved = match ($file_ext) {
                "jpg", "jpeg" => imagejpeg($dst_img, $upload_path, 85),
                "png" => imagepng($dst_img, $upload_path),
                "gif" => imagegif($dst_img, $upload_path),
            };

            imagedestroy($src_img);
            imagedestroy($dst_img);

            if ($saved) {
                if (!empty($img_name) && file_exists("avatars/" . $img_name)) {
                    unlink("avatars/" . $img_name);
                }
                $img_name = $new_name;
            } else {
                $message = "อัปโหลดรูปโปรไฟล์ไม่สำเร็จ";
            }
        }
    }

    if ($message === "") {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tbl_user SET name = ?, email = ?, bio = ?, address = ?, img = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $bio, $address, $img_name, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;
            $user["name"] = $name;
            $user["email"] = $email;
            $user["bio"] = $bio;
            $user["address"] = $address;
            $user["img"] = $img_name;
            $message = "บันทึกข้อมูลสำเร็จ";
        } else {
            $message = "บันทึกข้อมูลไม่สำเร็จ";
        }
    }
}
?>

<link rel="stylesheet" href="style.css">
<?php
include 'navbar.php';
?>

<div class="form-container">
<h2>แก้ไขโปรไฟล์</h2>

<?php if (!empty($user["img"])): ?>
    <img src="avatars/<?= htmlspecialchars($user["img"]) ?>" class="profile-img">
    <a href="delete_avatar.php" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรูปโปรไฟล์นี้?')">ลบรูปโปรไฟล์</a>
<?php endif; ?>

<form action="profile_update.php" method="post" enctype="multipart/form-data">
    <input type="text" name="name" value="<?= htmlspecialchars($user["name"]) ?>" class="input-field" placeholder="ชื่อ" required><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" class="input-field" placeholder="อีเมล" required><br>
    <input type="text" name="bio" value="<?= htmlspecialchars($user["bio"] ?? "") ?>" class="input-field" placeholder="แนะนำตัวเอง"><br>
    <input type="text" name="address" value="<?= htmlspecialchars($user["address"] ?? "") ?>" class="input-field" placeholder="ที่อยู่"><br>
    <input type="file" name="img" class="input-field"><br>

    <button type="submit">บันทึก</button>
</form>

<p><?= $message ?></p>
<a href="profile.php">กลับไปโปรไฟล์</a>

</div>
