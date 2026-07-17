<?php
// บอร์ดเรียกเพื่อดูว่ารีเลย์ช่องไหนต้องเปิด/ปิด
// GET api_relay.php?key=mysecretkey123
// ตอบ: {"ok":true,"relays":{"1":0,"2":1,...}}
include 'config.php';
include 'iot_config.php';

iot_check_key();

$result = mysqli_query($conn, "SELECT id, state FROM relays ORDER BY id");

$relays = [];
while ($row = mysqli_fetch_assoc($result)) {
    $relays[$row['id']] = (int) $row['state'];
}

iot_json(['ok' => true, 'relays' => $relays]);
?>
