<?php
// หน้า dashboard เรียกทุก 2 วินาที เพื่อดึงสถานะล่าสุด + ข้อมูลกราฟ
session_start();
include 'config.php';
include 'iot_config.php';

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    iot_json(['ok' => false, 'error' => 'กรุณาเข้าสู่ระบบ']);
}

// รีเลย์
$relays = [];
$result = mysqli_query($conn, "SELECT id, name, state FROM relays ORDER BY id");
while ($row = mysqli_fetch_assoc($result)) {
    $relays[] = [
        'id'    => (int) $row['id'],
        'name'  => $row['name'],
        'state' => (int) $row['state'],
    ];
}

// สวิตช์
$switches = [];
$result = mysqli_query($conn, "SELECT id, name, state, press_count, updated_at FROM switches ORDER BY id");
while ($row = mysqli_fetch_assoc($result)) {
    $switches[] = [
        'id'          => (int) $row['id'],
        'name'        => $row['name'],
        'state'       => (int) $row['state'],
        'press_count' => (int) $row['press_count'],
        'updated_at'  => $row['updated_at'],
    ];
}

// ค่า LDR 30 ค่าล่าสุด (เรียงเก่า -> ใหม่ เพื่อวาดกราฟ)
$ldr    = [];
$result = mysqli_query(
    $conn,
    "SELECT value, created_at FROM (
        SELECT id, value, created_at FROM ldr_readings ORDER BY id DESC LIMIT 30
     ) AS t ORDER BY id ASC"
);
while ($row = mysqli_fetch_assoc($result)) {
    $ldr[] = [
        'value' => (int) $row['value'],
        'time'  => date('H:i:s', strtotime($row['created_at'])),
    ];
}

iot_json([
    'ok'       => true,
    'relays'   => $relays,
    'switches' => $switches,
    'ldr'      => $ldr,
]);
?>
