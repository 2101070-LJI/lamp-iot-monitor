#!/bin/bash
# ============================================================
# LAMP Stack Auto-Installation Script for Ubuntu 24.04
# LAMP 스택 자동 설치 스크립트
# ============================================================

set -e

echo "=============================================="
echo "  LAMP Stack Installation - Ubuntu 24.04"
echo "=============================================="

# 1. 시스템 업데이트
echo "[1/6] Updating system packages..."
sudo apt-get update -y
sudo apt-get upgrade -y

# 2. Apache2 설치
echo "[2/6] Installing Apache2..."
sudo apt-get install -y apache2
sudo systemctl start apache2
sudo systemctl enable apache2
echo "  Apache2 installed and started."

# 3. MySQL (MariaDB) 설치
echo "[3/6] Installing MySQL (MariaDB)..."
sudo apt-get install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
echo "  MySQL installed and started."

# 4. PHP 설치
echo "[4/6] Installing PHP and extensions..."
sudo apt-get install -y php php-mysql php-cli php-json php-common libapache2-mod-php
echo "  PHP installed."

# 5. Python 패키지 설치
echo "[5/6] Installing Python packages..."
sudo apt-get install -y python3 python3-pip python3-venv
pip3 install pymysql faker --break-system-packages 2>/dev/null || \
  pip3 install pymysql faker
echo "  Python packages installed."

# 6. 데이터베이스 초기화
echo "[6/6] Setting up database..."
sudo mysql < /home/$USER/Desktop/project2/setup/setup_db.sql
echo "  Database initialized."

# Apache 재시작
sudo systemctl restart apache2

# 웹 파일 배포
echo "Deploying web files..."
sudo cp /home/$USER/Desktop/project2/web/*.php /var/www/html/
sudo cp /home/$USER/Desktop/project2/web/*.css /var/www/html/ 2>/dev/null || true
sudo chown -R www-data:www-data /var/www/html/

echo ""
echo "=============================================="
echo "  Installation Complete!"
echo "  Web Dashboard: http://localhost/index.php"
echo "  Run injector: python3 injector.py"
echo "=============================================="

# 서비스 상태 확인
echo ""
echo "Service Status:"
echo "  Apache: $(systemctl is-active apache2)"
echo "  MySQL:  $(systemctl is-active mysql)"
