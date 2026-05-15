# Quick Server Configuration for tanzaniaship.com
# For Server Owners

## Prerequisites
- SSH access to your server (178.16.128.28)
- Root or sudo access

---

## APACHE Configuration

### Step 1: Find Your Apache Config

Common locations:
```bash
# Check which config file is active
apache2ctl -S

# Usually one of these:
ls -la /etc/apache2/sites-available/
ls -la /etc/apache2/sites-enabled/
```

### Step 2: Edit the VirtualHost

```bash
# Edit your main site config (adjust path if needed)
sudo nano /etc/apache2/sites-available/000-default.conf
```

### Step 3: Add ServerAlias

Find your `<VirtualHost *:80>` block and add the ServerAlias line:

```apache
<VirtualHost *:80>
    ServerName redsharkpro.com
    ServerAlias www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com
    
    DocumentRoot /var/www/html/public
    # OR wherever your Laravel public folder is
    
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### Step 4: Do the Same for HTTPS (Port 443)

```bash
# Edit SSL config
sudo nano /etc/apache2/sites-available/000-default-ssl.conf
# OR
sudo nano /etc/apache2/sites-available/default-ssl.conf
```

Add the same ServerAlias to the `<VirtualHost *:443>` block:

```apache
<VirtualHost *:443>
    ServerName redsharkpro.com
    ServerAlias www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com
    
    # ... rest of your SSL config
</VirtualHost>
```

### Step 5: Test and Restart

```bash
# Test configuration
sudo apache2ctl configtest

# If OK, restart Apache
sudo systemctl restart apache2

# Check status
sudo systemctl status apache2
```

---

## NGINX Configuration

### Step 1: Find Your Nginx Config

```bash
# Find your config file
ls -la /etc/nginx/sites-available/
ls -la /etc/nginx/sites-enabled/
ls -la /etc/nginx/conf.d/
```

### Step 2: Edit the Server Block

```bash
# Edit your main config (adjust path if needed)
sudo nano /etc/nginx/sites-available/default
# OR
sudo nano /etc/nginx/conf.d/default.conf
```

### Step 3: Add to server_name

Find your `server` block and add the domains to server_name:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name redsharkpro.com www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com;
    
    root /var/www/html/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Step 4: Do the Same for HTTPS

```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name redsharkpro.com www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com;
    
    # ... rest of your SSL config
}
```

### Step 5: Test and Restart

```bash
# Test configuration
sudo nginx -t

# If OK, restart Nginx
sudo systemctl restart nginx

# Check status
sudo systemctl status nginx
```

---

## After Configuration

### 1. Test HTTP
```bash
curl -I http://tanzaniaship.com
```

Should return 200 OK and show your website

### 2. Test DNS
```bash
nslookup tanzaniaship.com
```

Should show: 178.16.128.28

### 3. Visit in Browser
http://tanzaniaship.com - Should show your store!

---

## Install SSL Certificate (Step 4)

After HTTP is working, add SSL:

```bash
# Install Certbot if not installed
sudo apt update
sudo apt install certbot python3-certbot-apache
# OR for Nginx:
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --apache -d tanzaniaship.com -d www.tanzaniaship.com
# OR for Nginx:
sudo certbot --nginx -d tanzaniaship.com -d www.tanzaniaship.com

# Follow the prompts
# Choose: Redirect HTTP to HTTPS (option 2)
```

### Test HTTPS
```bash
curl -I https://tanzaniaship.com
```

Visit: https://tanzaniaship.com 🔒

---

## Troubleshooting

### "Connection refused"
```bash
# Check if web server is running
sudo systemctl status apache2
# OR
sudo systemctl status nginx

# Check if port 80/443 is open
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :443

# Check firewall
sudo ufw status
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### "403 Forbidden"
```bash
# Check file permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

### View Logs
```bash
# Apache logs
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/access.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

---

## Quick Commands Summary

```bash
# APACHE
sudo nano /etc/apache2/sites-available/000-default.conf
# Add: ServerAlias tanzaniaship.com www.tanzaniaship.com
sudo apache2ctl configtest
sudo systemctl restart apache2

# NGINX
sudo nano /etc/nginx/sites-available/default
# Add to server_name: tanzaniaship.com www.tanzaniaship.com
sudo nginx -t
sudo systemctl restart nginx

# SSL (after HTTP works)
sudo certbot --apache -d tanzaniaship.com -d www.tanzaniaship.com
# OR
sudo certbot --nginx -d tanzaniaship.com -d www.tanzaniaship.com
```

---

**Need help?** Check the logs or visit the checker tool:
https://redsharkpro.com/check-server.php?domain=tanzaniaship.com
