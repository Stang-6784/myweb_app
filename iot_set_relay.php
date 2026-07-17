<?php
// หน้าเว็บเรียกเพื่อสั่งเปิด/ปิดรีเลย์ (ต้องล็อกอินก่อน)
session_start();
include 'config.php';
include 'iot_config.php';

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    iot_json(['ok' => false, 'error' => 'กรุณาเข้าสู่ระบบ']);
}

$id    = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$state = isset($_POST['state']) ? (int) $_POST['state'] : -1;

if ($id < 1 || $id > 5 || ($state !== 0 && $state !== 1)) {
    http_response_code(400);
    iot_json(['ok' => false, 'error' => 'ค่าไม่ถูกต้อง']);
}

$stmt = mysqli_prepare($conn, "UPDATE relays SET state = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ii", $state, $id);
mysqli_stmt_execute($stmt);

iot_json(['ok' => true, 'id' => $id, 'state' => $state]);
?>
