# Project: LAMP Stack Real-Time Sensor Data Monitoring System

## 과목 정보
- 과목: 캡스톤 디자인 / 임베디드 리눅스
- 주차: 3주차 과제

## 프로젝트 개요

VMware에 Ubuntu 24.04를 설치하고 LAMP 스택(Linux, Apache, MySQL, PHP)을 구성하여,
Python으로 가상의 센서 데이터를 생성·주입하고, PHP 동적 웹페이지를 통해 실시간으로
모니터링하는 시스템을 구축합니다.

## 하려는 것 (What We Are Trying To Do)

### 1. 환경 구축
- VMware Workstation/Player에 Ubuntu 24.04 LTS 가상머신 생성
- LAMP Stack 설치 및 구성
  - **L**inux: Ubuntu 24.04
  - **A**pache: Apache2 웹서버
  - **M**ySQL: MariaDB/MySQL 데이터베이스
  - **P**HP: PHP 8.x 동적 웹페이지 생성

### 2. 데이터 생성 및 저장 (injector.py)
- Python 스크립트를 작성하여 가상의 IoT 센서 데이터 생성
  - 온도(Temperature), 습도(Humidity), 기압(Pressure), 조도(Light) 등
- 생성된 데이터를 MySQL 데이터베이스의 특정 테이블에 주기적으로 저장
- `faker`, `pymysql` 라이브러리 활용

### 3. 실시간 모니터링 웹페이지 (PHP)
- PHP를 사용하여 MySQL 데이터를 읽어 동적 HTML 페이지 생성
- Chart.js를 이용한 실시간 그래프 시각화
- 메타 리프레시 또는 AJAX를 통한 자동 갱신
- 최신 데이터 테이블 및 통계 표시

### 4. GitHub 관리
- 프로젝트 전체를 GitHub 레포지토리에 push
- `process.md`에 시스템 블록도(Mermaid) 포함한 작업 과정 문서화
- README.md 작성

## 프로젝트 구조

```
project2/
├── project.md          # 프로젝트 설명 (본 파일)
├── process.md          # 작업 과정 및 Mermaid 블록도
├── README.md           # GitHub 저장소 소개
├── injector.py         # Python 데이터 생성·주입 스크립트
├── setup/
│   ├── install_lamp.sh # LAMP 스택 자동 설치 스크립트
│   ├── setup_db.sql    # 데이터베이스 및 테이블 초기화 SQL
│   └── requirements.txt # Python 패키지 목록
├── web/
│   ├── index.php       # 메인 모니터링 대시보드
│   ├── monitor.php     # 실시간 데이터 표시 페이지
│   ├── api.php         # AJAX용 JSON 데이터 API
│   └── style.css       # 스타일시트
└── docs/
    └── submission.txt  # 제출용 정보 (GitHub repo, 동작영상 링크)
```

## 기대 결과
- 가상머신 위에서 완전히 동작하는 LAMP 기반 IoT 데이터 모니터링 시스템
- 실시간으로 업데이트되는 웹 대시보드
- 재현 가능한 설치 스크립트와 문서화된 과정
