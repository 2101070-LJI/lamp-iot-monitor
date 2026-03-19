#!/usr/bin/env python3
"""
injector.py - 가상 IoT 센서 데이터 생성 및 MySQL 주입 프로그램
캡스톤 디자인 3주차 과제

사용법:
    python3 injector.py              # 기본 실행 (5초 간격)
    python3 injector.py --interval 2 # 2초 간격으로 실행
    python3 injector.py --once       # 1회만 실행
"""

import argparse
import random
import time
import signal
import sys
from datetime import datetime

import pymysql
from faker import Faker

# ── 데이터베이스 접속 설정 ──────────────────────────────────────
DB_CONFIG = {
    "host":     "localhost",
    "port":     3306,
    "user":     "sensor_user",
    "password": "sensor_pass123",
    "database": "sensor_db",
    "charset":  "utf8mb4",
}

# ── 가상 센서 목록 ──────────────────────────────────────────────
SENSORS = [
    {"id": "SENS-001", "name": "Room A Sensor",   "temp_base": 24.0, "humid_base": 55.0},
    {"id": "SENS-002", "name": "Room B Sensor",   "temp_base": 22.0, "humid_base": 60.0},
    {"id": "SENS-003", "name": "Outdoor Sensor",  "temp_base": 18.0, "humid_base": 72.0},
    {"id": "SENS-004", "name": "Server Room",     "temp_base": 20.0, "humid_base": 40.0},
    {"id": "SENS-005", "name": "Warehouse Sensor","temp_base": 15.0, "humid_base": 65.0},
]

INSERT_SQL = """
    INSERT INTO sensor_data
        (sensor_id, sensor_name, temperature, humidity, pressure, light, status)
    VALUES
        (%s, %s, %s, %s, %s, %s, %s)
"""

fake = Faker()
running = True


def signal_handler(sig, frame):
    """Ctrl+C 처리 - 깔끔하게 종료"""
    global running
    print("\n[INFO] Stopping injector... Goodbye!")
    running = False


def generate_reading(sensor: dict) -> tuple:
    """센서 한 개의 가상 측정값 생성"""
    temperature = round(sensor["temp_base"]  + random.uniform(-3.0,  3.0),  2)
    humidity    = round(sensor["humid_base"] + random.uniform(-5.0,  5.0),  2)
    pressure    = round(1013.25              + random.uniform(-10.0, 10.0), 2)
    light       = random.randint(0, 1000)

    # 비정상 값 시뮬레이션 (5% 확률)
    status = "WARN" if random.random() < 0.05 else "OK"

    # 값 범위 클램핑
    humidity    = max(0.0, min(100.0, humidity))
    temperature = max(-40.0, min(85.0, temperature))

    return (sensor["id"], sensor["name"],
            temperature, humidity, pressure, light, status)


def connect_db():
    """MySQL 연결 반환. 실패 시 재시도."""
    while running:
        try:
            conn = pymysql.connect(**DB_CONFIG)
            print("[INFO] Connected to MySQL database.")
            return conn
        except pymysql.MySQLError as e:
            print(f"[ERROR] DB connection failed: {e}")
            print("[INFO] Retrying in 5 seconds...")
            time.sleep(5)
    return None


def inject_once(conn) -> int:
    """모든 센서의 데이터를 한 번 주입. 삽입된 행 수 반환."""
    rows = [generate_reading(s) for s in SENSORS]
    with conn.cursor() as cur:
        cur.executemany(INSERT_SQL, rows)
    conn.commit()
    return len(rows)


def run(interval: float, once: bool):
    """메인 루프"""
    conn = connect_db()
    if conn is None:
        return

    total = 0
    try:
        while running:
            count = inject_once(conn)
            total += count
            ts = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            print(f"[{ts}] Inserted {count} rows | Total: {total}")

            if once:
                break
            time.sleep(interval)

    except pymysql.MySQLError as e:
        print(f"[ERROR] DB error during injection: {e}")
    finally:
        conn.close()
        print(f"[INFO] Connection closed. Total rows inserted: {total}")


# ── CLI 진입점 ──────────────────────────────────────────────────
if __name__ == "__main__":
    signal.signal(signal.SIGINT, signal_handler)

    parser = argparse.ArgumentParser(
        description="Virtual IoT sensor data injector for MySQL"
    )
    parser.add_argument(
        "--interval", type=float, default=5.0,
        help="Injection interval in seconds (default: 5)"
    )
    parser.add_argument(
        "--once", action="store_true",
        help="Insert data once and exit"
    )
    args = parser.parse_args()

    print("=" * 50)
    print("  IoT Sensor Data Injector")
    print(f"  Sensors : {len(SENSORS)}")
    print(f"  Interval: {args.interval}s" if not args.once else "  Mode    : single shot")
    print("  Press Ctrl+C to stop")
    print("=" * 50)

    run(interval=args.interval, once=args.once)
