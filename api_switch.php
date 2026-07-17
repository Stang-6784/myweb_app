<?php
// บอร์ดส่งสถานะสวิตช์เข้ามา
// POST api_switch.php  key=mysecretkey123&id=1&state=1
include 'config.php';
include 'iot_config.php';

iot_check_key();

$id    = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
$state = isset($_REQUEST['state']) ? (int) $_REQUEST['state'] : -1;

if ($id < 1 || $id > 2 || ($state !== 0 && $state !== 1)) {
    http_response_code(400);
    iot_json(['ok' => false, 'error' => 'id ต้องเป็น 1-2 และ state ต้องเป็น 0 หรือ 1']);
}

// อัปเดตสถานะล่าสุด และนับจำนวนครั้งที่กด (นับตอนเปลี่ยนเป็น 1)
$stmt = mysqli_prepare(
    $conn,
    "UPDATE switches SET state = ?, press_count = press_count + ? WHERE id = ?"
);
$inc = $state === 1 ? 1 : 0;
mysqli_stmt_bind_param($stmt, "iii", $state, $inc, $id);
mysqli_stmt_execute($stmt);

// เก็บประวัติ
$stmt = mysqli_prepare($conn, "INSERT INTO switch_logs (switch_id, state) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ii", $id, $state);
mysqli_stmt_execute($stmt);

iot_json(['ok' => true, 'id' => $id, 'state' => $state]);
?>
