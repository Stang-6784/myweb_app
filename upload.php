<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";
$user_id = $_SESSION["user_id"];

if (isset($_POST["upload"])) {

    $file_name = $_FILES["image"]["name"];
    $file_tmp  = $_FILES["image"]["tmp_name"];
    $file_size = $_FILES["image"]["size"];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $image_ext = ["jpg", "jpeg", "png", "gif"];
    $other_ext = ["pdf", "doc", "docx", "xls", "xlsx", "txt", "zip"];
    $allowed = array_merge($image_ext, $other_ext);

    $image_info = in_array($file_ext, $image_ext) ? @getimagesize($file_tmp) : false;

    if (!in_array($file_ext, $allowed)) {
        $message = "อนุญาตเฉพาะ JPG, JPEG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP";
    } elseif ($file_size > 2 * 1024 * 1024) {
        $message = "ไฟล์ต้องไม่เกิน 2MB";
    } elseif ($image_info && ($image_info[0] > 1920 || $image_info[1] > 1080)) {
        $message = "ขนาดรูปภาพต้องไม่เกิน 1920x1080 พิกเซล";
    } else {
        $file_type = in_array($file_ext, $image_ext) ? "image" : "file";
        $safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file_name));
        $new_name = file_exists("uploads/" . $safe_name) ? uniqid() . "_" . $safe_name : $safe_name;
        $upload_path = "uploads/" . $new_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO tbl_upload (user_id, image_name, file_type) VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "iss", $user_id, $new_name, $file_type);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Upload complete";
            } else {
                $message = "บันทึกฐานข้อมูลไม่สำเร็จ";
            }

        } else {
            $message = "Upload ไม่สำเร็จ";
        }
    }
}
echo '<link rel="stylesheet" href="style.css">';
include "navbar.php";
?>

<h2>Upload รูปภาพ</h2>

<p>
    ผู้ใช้งาน: <?php echo $_SESSION["name"]; ?> | อีเมล: <?php echo $_SESSION["email"]; ?>
</p>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <button type="submit" name="upload">Upload</button>
</form>

<p><?php echo $message; ?></p>

<hr>

<h2>รูปภาพของฉัน</h2>

<?php
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM tbl_upload WHERE user_id = ? ORDER BY id DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

echo "<div class='card-container'>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<div class='card'>";
    echo "<img src='uploads/" . htmlspecialchars($row["image_name"]) . "'><br>";
    echo "ชื่อไฟล์: " . htmlspecialchars($row["image_name"]) . "<br>";
    echo "<a href='delete_image.php?id=" . $row["id"] . "' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์นี้?\")'>ลบ</a>";
    echo "</div>";
}
echo "</div>";
?>