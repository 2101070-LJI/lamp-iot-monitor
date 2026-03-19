-- ============================================================
-- Database Setup Script
-- 데이터베이스 및 테이블 초기화
-- ============================================================

-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS sensor_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sensor_db;

-- 사용자 생성 및 권한 부여
CREATE USER IF NOT EXISTS 'sensor_user'@'localhost' IDENTIFIED BY 'sensor_pass123';
GRANT ALL PRIVILEGES ON sensor_db.* TO 'sensor_user'@'localhost';
FLUSH PRIVILEGES;

-- 센서 데이터 테이블 생성
CREATE TABLE IF NOT EXISTS sensor_data (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id   VARCHAR(20)    NOT NULL COMMENT '센서 식별자',
    sensor_name VARCHAR(50)    NOT NULL COMMENT '센서 이름',
    temperature DECIMAL(5, 2)  NOT NULL COMMENT '온도 (°C)',
    humidity    DECIMAL(5, 2)  NOT NULL COMMENT '습도 (%)',
    pressure    DECIMAL(7, 2)  NOT NULL COMMENT '기압 (hPa)',
    light       INT            NOT NULL COMMENT '조도 (lux)',
    status      VARCHAR(10)    NOT NULL DEFAULT 'OK' COMMENT '센서 상태',
    recorded_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '기록 시각',
    INDEX idx_sensor_id   (sensor_id),
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB COMMENT='IoT 센서 실시간 데이터';

-- 테스트용 초기 데이터 삽입
INSERT INTO sensor_data (sensor_id, sensor_name, temperature, humidity, pressure, light, status) VALUES
('SENS-001', 'Room A Sensor',   24.50, 55.30, 1013.25, 350, 'OK'),
('SENS-002', 'Room B Sensor',   22.10, 60.00, 1012.80, 200, 'OK'),
('SENS-003', 'Outdoor Sensor',  18.75, 72.50, 1010.00, 800, 'OK');

SELECT 'Database setup complete!' AS message;
SELECT COUNT(*) AS initial_rows FROM sensor_data;
