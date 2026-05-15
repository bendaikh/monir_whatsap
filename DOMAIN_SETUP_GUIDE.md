# Custom Domain Setup Guide for tanzaniaship.com

This guide will help you connect your domain **tanzaniaship.com** to your store hosted on **redsharkpro.com**.

## Overview

Your main application is hosted at: `redsharkpro.com`
Server IP Address: **178.16.128.28**
Custom Domain: **tanzaniaship.com**

## Step 1: Configure DNS in Hostinger

1. **Log into Hostinger** (https://www.hostinger.com)
2. Go to **Domains** → **Manage** → Select **tanzaniaship.com**
3. Click on **DNS / Nameservers** or **DNS Records**

### Add/Update These DNS Records:

```
Type: A
Name: @
Points to: 178.16.128.28
TTL: 14400 (or Auto)

Type: A
Name: www
Points to: 178.16.128.28
TTL: 14400 (or Auto)
```

**Note:** 
- The `@` record points the root domain (tanzaniaship.com) to your server
- The `www` record points www.tanzaniaship.com to your server
- DNS propagation can take 1-24 hours, but usually completes within 1-6 hours

## Step 2: Update Your Store with the Custom Domain

You have two options:

### Option A: Through the Web Interface (Recommended)

1. Log into your application at https://redsharkpro.com
2. Go to **Stores** management page
3. Select the store you want to connect (e.g., "Tanzania Ship")
4. Click **Edit**
5. In the **Custom Domain** field, enter: `tanzaniaship.com`
6. Click **Save**

### Option B: Directly Update the Database

If you have database access:

```sql
UPDATE stores 
SET domain = 'tanzaniaship.com' 
WHERE id = YOUR_STORE_ID;
```

Replace `YOUR_STORE_ID` with the actual ID of your store.

## Step 3: Configure Web Server (Apache/Nginx)

Your hosting provider needs to recognize both domains. Contact your hosting support or update your server configuration:

### For Apache (.htaccess or VirtualHost)

Add to your VirtualHost configuration:

```apache
<VirtualHost *:80>
    ServerName redsharkpro.com
    ServerAlias www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com
    
    DocumentRoot /path/to/your/laravel/public
    
    <Directory /path/to/your/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:443>
    ServerName redsharkpro.com
    ServerAlias www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com
    
    DocumentRoot /path/to/your/laravel/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    
    <Directory /path/to/your/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### For Nginx

Add to your server block:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name redsharkpro.com www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com;
    
    root /path/to/your/laravel/public;
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

## Step 4: Set Up SSL Certificate

### Option A: Use Let's Encrypt (Free SSL)

SSH into your server and run:

```bash
# For Apache
sudo certbot --apache -d tanzaniaship.com -d www.tanzaniaship.com

# For Nginx
sudo certbot --nginx -d tanzaniaship.com -d www.tanzaniaship.com
```

### Option B: Use Hostinger's Free SSL

1. In Hostinger hPanel
2. Go to **SSL** section
3. Select **tanzaniaship.com**
4. Enable **Free SSL Certificate**
5. Wait for activation (usually 10-30 minutes)

## Step 5: Test Your Setup

### Check DNS Propagation

Open Command Prompt and run:

```cmd
nslookup tanzaniaship.com
```

You should see the IP: `178.16.128.28`

### Test the Domain

1. Open a web browser
2. Go to: https://tanzaniaship.com
3. You should see your store!

### Troubleshooting

If it's not working:

1. **Wait for DNS propagation** (up to 24 hours)
2. **Clear your browser cache**: Ctrl+Shift+Delete
3. **Check DNS**: Use https://dnschecker.org to verify propagation
4. **Check the database**: Ensure the domain is correctly set in the stores table
5. **Check web server logs**: Look for any errors

## How It Works

1. When someone visits **tanzaniaship.com**, DNS points them to your server (178.16.128.28)
2. Your web server receives the request
3. The Laravel middleware `DetectCustomDomain` checks if the request is from a custom domain
4. If it matches a store's domain in the database, it loads that store
5. The visitor sees your store at tanzaniaship.com!

## Additional Configuration

### Update .env File (Production Server)

Make sure your `.env` file on the production server includes:

```env
APP_URL=https://redsharkpro.com
SESSION_DOMAIN=null
```

**Note:** Keep `SESSION_DOMAIN=null` to allow cookies to work across both domains.

### Force HTTPS

Add this to your `public/.htaccess` (if using Apache):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

## Testing with Multiple Stores

You can add multiple custom domains to different stores:

| Store Name | Subdomain | Custom Domain |
|------------|-----------|---------------|
| Tanzania Ship | tanzania | tanzaniaship.com |
| Store 2 | store2 | anotherdomain.com |
| Store 3 | store3 | thirddomain.com |

Each domain will automatically show its respective store!

## Support

If you encounter any issues:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Contact your hosting provider for server configuration help
3. Verify DNS records at: https://dnschecker.org

---

**Your setup is now complete! 🎉**

Once DNS propagates, **tanzaniaship.com** will show your store!
