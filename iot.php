<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ควบคุมรีเลย์และเซ็นเซอร์</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <h1>ควบคุมรีเลย์และเซ็นเซอร์</h1>

    <h2 class="section-title">รีเลย์ 5 ช่อง</h2>
    <div class="card-container" id="relay-container"></div>

    <h2 class="section-title">สถานะสวิตช์</h2>
    <div class="card-container" id="switch-container"></div>

    <h2 class="section-title">ค่าแสง (LDR)</h2>
    <div class="chart-box">
        <canvas id="ldrChart"></canvas>
    </div>

<script>
let chart;

function initChart() {
    const ctx = document.getElementById('ldrChart');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'ค่า LDR',
                data: [],
                borderColor: 'rgb(116, 207, 207)',
                backgroundColor: 'rgba(116, 207, 207, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'ค่าที่อ่านได้' } },
                x: { title: { display: true, text: 'เวลา' } }
            }
        }
    });
}

function drawRelays(relays) {
    const box = document.getElementById('relay-container');
    box.innerHTML = '';
    relays.forEach(function (r) {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML =
            '<h3>' + r.name + '</h3>' +
            '<div class="status-dot ' + (r.state ? 'on' : 'off') + '"></div>' +
            '<p>' + (r.state ? 'เปิด' : 'ปิด') + '</p>';

        const btn = document.createElement('button');
        btn.className = 'btn';
        btn.textContent = r.state ? 'สั่งปิด' : 'สั่งเปิด';
        btn.onclick = function () { setRelay(r.id, r.state ? 0 : 1); };
        card.appendChild(btn);

        box.appendChild(card);
    });
}

function drawSwitches(switches) {
    const box = document.getElementById('switch-container');
    box.innerHTML = '';
    switches.forEach(function (s) {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML =
            '<h3>' + s.name + '</h3>' +
            '<div class="status-dot ' + (s.state ? 'on' : 'off') + '"></div>' +
            '<p>' + (s.state ? 'กดอยู่' : 'ปล่อย') + '</p>' +
            '<p class="small">กดไปแล้ว ' + s.press_count + ' ครั้ง<br>ล่าสุด ' + s.updated_at + '</p>';
        box.appendChild(card);
    });
}

function setRelay(id, state) {
    const body = new URLSearchParams({ id: id, state: state });
    fetch('iot_set_relay.php', { method: 'POST', body: body })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.ok) { alert(data.error); }
            loadData();
        })
        .catch(function () { alert('สั่งงานไม่สำเร็จ'); });
}

function loadData() {
    fetch('iot_data.php')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.ok) { return; }
            drawRelays(data.relays);
            drawSwitches(data.switches);
            chart.data.labels = data.ldr.map(function (d) { return d.time; });
            chart.data.datasets[0].data = data.ldr.map(function (d) { return d.value; });
            chart.update();
        })
        .catch(function () { /* ถ้าดึงไม่ได้ รอบหน้าค่อยลองใหม่ */ });
}

initChart();
loadData();
setInterval(loadData, 2000);
</script>
</body>
</html>
