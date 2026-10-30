import os
from pathlib import Path

# Konfigurasi MySQL Lokal untuk Python
MYSQL_CONFIG = {
    'host': os.getenv('PYTHON_DB_HOST', '127.0.0.1'),
    'port': int(os.getenv('PYTHON_DB_PORT', '3306')),
    'user': os.getenv('PYTHON_DB_USER', 'root'),
    'password': os.getenv('PYTHON_DB_PASSWORD', ''),
    'database': os.getenv('PYTHON_DB_DATABASE', 'siapel_db'),
    'charset': 'utf8mb4',
    'collation': 'utf8mb4_unicode_ci'
}

# Konfigurasi Scanner QR Lokal
SCANNER_SETTINGS = {
    'qr_cooldown': int(os.getenv('QR_COOLDOWN_SECONDS', '30')),
    'default_laravel_url': os.getenv('LARAVEL_SERVER_URL', 'http://127.0.0.1:8000')
}

