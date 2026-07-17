<?php
// บอร์ดส่งค่า LDR เข้ามา
// POST api_ldr.php  key=mysecretkey123&value=512
include 'config.php';
include 'iot_config.php';

iot_check_key();

if (!isset($_REQUEST['value']) || !is_numeric($_REQUEST['value'])) {
    http_response_code(400);
    iot_json(['ok' => false, 'error' => 'ต้องส่ง value เป็นตัวเลข']);
}

$value = (int) $_REQUEST['value'];

$stmt = mysqli_prepare($conn, "INSERT INTO ldr_readings (value) VALUES (?)");
mysqli_stmt_bind_param($stmt, "i", $value);
mysqli_stmt_execute($stmt);

iot_json(['ok' => true, 'value' => $value]);
?>
