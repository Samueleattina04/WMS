# WMS Gestionale - Guida Deploy VPS Ubuntu 22.04

## Requisiti Server
- Ubuntu 22.04 LTS
- PHP 8.3+ con estensioni: bcmath, ctype, curl, dom, fileinfo, mbstring, openssl, pcre, pdo_mysql, tokenizer, xml, zip, gd
- MySQL 8.0+
- Nginx
- Composer 2
- Node.js 20+ e npm
- Redis (opzionale, per prod)

---

## 1. Preparazione Server

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-zip php8.3-curl php8.3-bcmath php8.3-gd php8.3-intl \
  php8.3-redis unzip git curl

# Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 2. MySQL Setup

```bash
sudo mysql_secure_installation

sudo mysql -u root -p
```

```sql
CREATE DATABASE wms_gestionale CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'wms'@'localhost' IDENTIFIED BY 'PASSWORD_SICURA_QUI';
GRANT ALL PRIVILEGES ON wms_gestionale.* TO 'wms'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Deploy Codice

```bash
sudo mkdir -p /var/www/wms
sudo chown www-data:www-data /var/www/wms
cd /var/www/wms

# Clone repository
git clone https://github.com/tuo-repo/wms.git .

# Dipendenze PHP
composer install --no-dev --optimize-autoloader

# Dipendenze frontend e build
npm ci && npm run build

# Configurazione ambiente
cp .env.example .env
php artisan key:generate
```

Modifica `/var/www/wms/.env`:

```ini
APP_NAME="WMS Gestionale"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gestionale.it

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wms_gestionale
DB_USERNAME=wms
DB_PASSWORD=PASSWORD_SICURA_QUI

TENANT_DOMAIN=gestionale.it
STOCK_ALERT_DAYS_BEFORE_EXPIRY=30

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=redis        # opzionale ma consigliato in prod

MAIL_MAILER=smtp
MAIL_HOST=smtp.tuoprovider.it
MAIL_PORT=465
MAIL_USERNAME=noreply@gestionale.it
MAIL_PASSWORD=password_smtp
MAIL_FROM_ADDRESS=noreply@gestionale.it
MAIL_FROM_NAME="WMS Gestionale"
```

```bash
# Migra DB e seed (solo primo deploy)
php artisan migrate --force
php artisan db:seed --force

# Ottimizzazione produzione
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Permessi
sudo chown -R www-data:www-data /var/www/wms
sudo chmod -R 755 /var/www/wms/storage
sudo chmod -R 755 /var/www/wms/bootstrap/cache
```

---

## 4. Configurazione Nginx Wildcard Subdomain

Creare `/etc/nginx/sites-available/wms`:

```nginx
# HTTP → HTTPS redirect
server {
    listen 80;
    server_name gestionale.it *.gestionale.it;
    return 301 https://$host$request_uri;
}

# HTTPS principale
server {
    listen 443 ssl http2;
    server_name gestionale.it *.gestionale.it;

    ssl_certificate /etc/letsencrypt/live/gestionale.it/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/gestionale.it/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/wms/public;
    index index.php;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache assets statici
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/wms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 5. SSL con Let's Encrypt (Wildcard)

```bash
sudo apt install certbot python3-certbot-nginx -y

# Wildcard richiede DNS challenge (aggiornare record TXT DNS)
sudo certbot certonly --manual --preferred-challenges dns \
  -d gestionale.it -d *.gestionale.it

# Auto-rinnovo
sudo systemctl enable certbot.timer
```

---

## 6. Queue Worker con Supervisor

```bash
sudo apt install supervisor -y
```

Creare `/etc/supervisor/conf.d/wms-worker.conf`:

```ini
[program:wms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/wms/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/wms/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start wms-worker:*
```

---

## 7. Cron per Scheduled Tasks

```bash
sudo -u www-data crontab -e
```

Aggiungere:
```
* * * * * cd /var/www/wms && php artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Aggiungere un Nuovo Cliente (Tenant)

```bash
php artisan tinker
```

```php
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$company = Company::create([
    'name'              => 'Nome Azienda Srl',
    'slug'              => 'nomeazienda',   // → nomeazienda.gestionale.it
    'email'             => 'info@azienda.it',
    'subscription_plan' => 'professional',
    'is_active'         => true,
    'settings'          => ['alert_days_before_expiry' => 30, 'alert_emails' => ['admin@azienda.it']],
]);

User::create([
    'company_id' => $company->id,
    'name'       => 'Admin Azienda',
    'email'      => 'admin@azienda.it',
    'password'   => Hash::make('password-da-cambiare'),
    'role'       => 'admin',
    'is_active'  => true,
]);
```

Configurare il record DNS wildcard sul DNS provider:
```
*.gestionale.it   A   <IP_SERVER>
gestionale.it     A   <IP_SERVER>
```

---

## 9. Aggiornamenti Applicazione

```bash
cd /var/www/wms

git pull origin main

composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo supervisorctl restart wms-worker:*
sudo systemctl reload php8.3-fpm
```

---

## 10. Backup Automatico

```bash
# Script backup /usr/local/bin/wms-backup.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M)
BACKUP_DIR=/var/backups/wms

mkdir -p $BACKUP_DIR

# Backup DB
mysqldump -u wms -p'PASSWORD_SICURA_QUI' wms_gestionale | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/wms/storage/app

# Rimuovi backup più vecchi di 30 giorni
find $BACKUP_DIR -mtime +30 -delete
```

```bash
sudo chmod +x /usr/local/bin/wms-backup.sh
# Cron giornaliero alle 2:00
echo "0 2 * * * root /usr/local/bin/wms-backup.sh" | sudo tee /etc/cron.d/wms-backup
```

---

## Credenziali Demo (dopo `php artisan db:seed`)

| Ruolo          | Email                    | Password  |
|----------------|--------------------------|-----------|
| Amministratore | admin@demo.it            | password  |
| Responsabile   | manager@demo.it          | password  |
| Magazziniere   | magazziniere@demo.it     | password  |

Accesso: `http://demo.gestionale.it` (o aggiungere `127.0.0.1 demo.gestionale.it` in `/etc/hosts` per sviluppo locale)
