-- นำเข้าไฟล์นี้ใน phpMyAdmin (เลือกฐานข้อมูล myweb ก่อน)
USE myweb;

-- ตารางรีเลย์ 5 ช่อง
CREATE TABLE IF NOT EXISTS relays (
  id INT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  state TINYINT(1) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO relays (id, name, state) VALUES
  (1, 'Relay 1', 0),
  (2, 'Relay 2', 0),
  (3, 'Relay 3', 0),
  (4, 'Relay 4', 0),
  (5, 'Relay 5', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ตารางสวิตช์ (สถานะล่าสุดของแต่ละตัว)
CREATE TABLE IF NOT EXISTS switches (
  id INT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  state TINYINT(1) NOT NULL DEFAULT 0,
  press_count INT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO switches (id, name, state) VALUES
  (1, 'Switch 1', 0),
  (2, 'Switch 2', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ประวัติการกดสวิตช์
CREATE TABLE IF NOT EXISTS switch_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  switch_id INT NOT NULL,
  state TINYINT(1) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (switch_id, created_at)
);

-- ค่าที่อ่านได้จาก LDR
CREATE TABLE IF NOT EXISTS ldr_readings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  value INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (created_at)
);
