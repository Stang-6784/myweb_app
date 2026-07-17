<?php
// คีย์สำหรับให้บอร์ด (ESP32/ESP8266) ยืนยันตัวตนกับ API
// เปลี่ยนเป็นค่าของตัวเอง แล้วใส่ค่าเดียวกันในโค้ดบอร์ด
define('IOT_API_KEY', 'mysecretkey123');

function iot_check_key()
{
    $key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
    if (!hash_equals(IOT_API_KEY, $key)) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'invalid key']);
        exit;
    }
}

function iot_json($data)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
?>
