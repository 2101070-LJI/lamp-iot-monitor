# LAMP Stack IoT 실시간 센서 모니터링 시스템

> 캡스톤 디자인 / 임베디드 리눅스 — 3주차 과제

## 개요

VMware 위의 Ubuntu 24.04에 LAMP 스택을 구축하고,
Python으로 가상 IoT 센서 데이터를 MySQL에 주입하여
PHP 동적 웹페이지로 실시간 모니터링하는 시스템입니다.

## 시스템 구성

```
Python injector.py  →  MySQL (sensor_db)  →  PHP  →  Apache  →  브라우저
      (데이터 생성)        (저장)            (렌더링)   (서빙)    (5초 갱신)
```

## 빠른 시작

### 1. LAMP 스택 설치
```bash
chmod +x setup/install_lamp.sh
bash setup/install_lamp.sh
```

### 2. 데이터 주입 시작
```bash
python3 injector.py
# Ctrl+C 로 중지
```

### 3. 웹 파일 배포
```bash
sudo cp web/*.php web/style.css /var/www/html/
```

### 4. 브라우저에서 확인
```
http://localhost/index.php    ← 메인 대시보드
http://localhost/monitor.php  ← 텍스트 모니터
http://localhost/api.php      ← JSON API
```

## 주요 파일

| 파일 | 설명 |
|------|------|
| `injector.py` | 가상 센서 데이터 생성·MySQL 주입 (5초 간격) |
| `web/index.php` | Chart.js 기반 실시간 대시보드 |
| `web/monitor.php` | 터미널 스타일 모니터링 페이지 |
| `web/api.php` | JSON REST API 엔드포인트 |
| `setup/install_lamp.sh` | LAMP 자동 설치 스크립트 |
| `setup/setup_db.sql` | DB/테이블 초기화 SQL |
| `process.md` | 전체 구현 과정 및 Mermaid 시스템 블록도 |

## 기술 스택

- **OS**: Ubuntu 24.04 LTS (VMware)
- **Web Server**: Apache 2.4
- **Database**: MySQL / MariaDB
- **Backend**: PHP 8.x
- **Data Injector**: Python 3 + pymysql + faker
- **Frontend**: Chart.js, Vanilla JS

## 모니터링 센서 목록

| 센서 ID | 위치 | 측정값 |
|---------|------|--------|
| SENS-001 | Room A | 온도, 습도, 기압, 조도 |
| SENS-002 | Room B | 온도, 습도, 기압, 조도 |
| SENS-003 | Outdoor | 온도, 습도, 기압, 조도 |
| SENS-004 | Server Room | 온도, 습도, 기압, 조도 |
| SENS-005 | Warehouse | 온도, 습도, 기압, 조도 |

## 상세 문서

- [전체 구현 과정 및 시스템 블록도 → process.md](./process.md)
- [프로젝트 설명 → project.md](./project.md)
