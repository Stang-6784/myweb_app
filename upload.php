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

    $allowed = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($file_ext, $allowed)) {
        $message = "อนุญาตเฉพาะ JPG, JPEG, PNG, GIF";
    } elseif ($file_size > 2 * 1024 * 1024) {
        $message = "ไฟล์ต้องไม่เกิน 2MB";
    } else {
        $new_name = uniqid("IMG_", true) . "." . $file_ext;
        $upload_path = "uploads/" . $new_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO tbl_upload (user_id, image_name) VALUES (?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "is", $user_id, $new_name);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Upload สำเร็จ";
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
    echo "<img src='uploads/" . htmlspecialchars($row["image_name"]) . "' style='width:100%;'><br>";
    echo "ชื่อไฟล์: " . htmlspecialchars($row["image_name"]);
    echo "</div>";
}
echo "</div>";
?>