# Deploy Laravel App to Hostinger

## Quick Check: Is Your Laravel App Already on Hostinger?

Visit: https://mediumaquamarine-gazelle-424101.hostingersite.com

**What do you see?**

### If you see your Laravel app homepage:
✅ Laravel is deployed - skip to "Configure Laravel for Production" section below

### If you see "Coming Soon" or blank page:
❌ Laravel is NOT deployed - follow "Deploy Laravel" section below

### If you see "404 Not Found":
❌ Configuration issue - follow "Fix Configuration" section below

---

## Deploy Laravel to Hostinger

### Method 1: Using Git (Recommended)

1. **In Hostinger hPanel**, go to **Advanced** → **Git**
2. Click **"Create Repository"**
3. Configure:
   - **Repository URL**: Your GitHub/GitLab repo URL
   - **Branch**: `main` or `master`
   - **Deploy Path**: `/domains/mediumaquamarine-gazelle-424101.hostingersite.com`
4. Click **"Create"**
5. Wait for deployment to complete

### Method 2: Upload Files via File Manager

1. **On your local machine**, create a zip of your Laravel app:
   - Exclude `node_modules/`
   - Exclude `vendor/` (will reinstall on server)
   - Include everything else

2. **In Hostinger File Manager**:
   - Upload the zip to the root folder
   - Extract it
   - Move contents to the correct location

3. **Install Composer Dependencies**:
   - SSH into Hostinger (if available)
   - Run: `composer install --no-dev --optimize-autoloader`

---

## Configure Laravel for Production

Once Laravel files are on Hostinger, do these steps:

### Step 1: Setup .env File

In your Laravel root directory (where `public_html` is), edit `.env`:

```env
APP_NAME=ChatEasy
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://mediumaquamarine-gazelle-424101.hostingersite.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_hostinger_database_name
DB_USERNAME=your_hostinger_database_user
DB_PASSWORD=your_hostinger_database_password

# WhatsApp Service
WHATSAPP_SERVICE_URL=https://mediumaquamarine-gazelle-424101.hostingersite.com

# Other settings...
```

**Important Database Settings:**
- Get these from Hostinger hPanel → **Databases** section
- Hostinger creates a database for you automatically

### Step 2: Point public_html to Laravel's public folder

Hostinger expects files to be in `public_html`, but Laravel's entry point is `public/index.php`.

**Option A: Symlink (Recommended)**
```bash
# Delete the default public_html
rm -rf public_html
# Create symlink to Laravel's public folder
ln -s /path/to/laravel/public public_html
```

**Option B: Move Files**
- Move everything from `public/` into `public_html/`
- Update `index.php` to point to correct paths

### Step 3: Run Migrations

```bash
php artisan migrate --force
```

### Step 4: Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

---

## Fix Configuration Issues

### Issue: "404 Not Found" on /app/whatsapp

**Cause**: Routes not working, mod_rewrite issue

**Fix**: Ensure `.htaccess` exists in `public_html/`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Issue: "500 Internal Server Error"

**Cause**: Permissions, missing APP_KEY, or config cache

**Fix**:
1. Check storage permissions: `chmod -R 755 storage`
2. Generate APP_KEY: `php artisan key:generate`
3. Clear cache: `php artisan config:clear`
4. Check error logs in `storage/logs/laravel.log`

### Issue: Database Connection Error

**Cause**: Wrong database credentials in `.env`

**Fix**:
1. Go to Hostinger hPanel → **Databases**
2. Get correct credentials
3. Update `.env` file
4. Run: `php artisan config:clear`

---

## Quick Setup Script (SSH)

If you have SSH access, run these commands:

```bash
# Navigate to your Laravel root
cd /domains/mediumaquamarine-gazelle-424101.hostingersite.com

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
chmod -R 755 storage bootstrap/cache

# Generate key (if not set)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link
```

---

## Verify Deployment

After setup, check these URLs:

1. **Homepage**: https://mediumaquamarine-gazelle-424101.hostingersite.com
   - Should show your Laravel app

2. **WhatsApp Page** (requires login): https://mediumaquamarine-gazelle-424101.hostingersite.com/app/whatsapp
   - Should show WhatsApp management page

3. **WhatsApp Service API**: https://mediumaquamarine-gazelle-424101.hostingersite.com/api/status
   - Should return: `{"success":true,"activeSessions":0}`

---

## Common Hostinger-Specific Issues

### PHP Version
Make sure you're using PHP 8.1 or higher:
- Go to hPanel → **Advanced** → **PHP Configuration**
- Select PHP 8.1 or 8.2

### Composer
Hostinger usually has Composer pre-installed. If not:
- Use hPanel's built-in terminal
- Or contact support to install it

### File Structure
Hostinger expects this structure:
```
/domains/mediumaquamarine-gazelle-424101.hostingersite.com/
├── public_html/          ← Must be Laravel's public folder
│   ├── index.php
│   ├── .htaccess
│   └── ...
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
└── composer.json
```

---

## Next Steps

1. **Check if Laravel is deployed**: Visit your domain homepage
2. **If not deployed**: Follow the deployment steps above
3. **Configure .env**: Set database and WhatsApp service URL
4. **Test the app**: Try to access `/app/whatsapp` after logging in
5. **Connect WhatsApp**: Test the QR code connection

**Need help?** Let me know what you see when you visit:
- https://mediumaquamarine-gazelle-424101.hostingersite.com (homepage)
- What's in your `public_html` folder in File Manager
