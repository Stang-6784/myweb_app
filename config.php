<?php
// session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myweb";



$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// ระบุให้ชัดเจนว่าคุยกับ MySQL ด้วย utf8mb4 กันภาษาไทยเพี้ยน
mysqli_set_charset($conn, "utf8mb4");

?>