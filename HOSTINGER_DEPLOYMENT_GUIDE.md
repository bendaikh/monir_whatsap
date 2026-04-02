# Hostinger Deployment Guide for ChatEasy

This guide will help you deploy your Laravel + Node.js WhatsApp service on Hostinger.

## Architecture Overview

- **Main Domain** (manite.site): Laravel application
- **Subdomain** (whatsapp.manite.site): Node.js WhatsApp service

## Step-by-Step Deployment Instructions

### Phase 1: Deploy Laravel Application (Main Domain)

1. **Upload Laravel Files to Hostinger**
   - Connect via FTP or File Manager
   - Upload all Laravel files EXCEPT the `whatsapp-service` folder
   - Place files in the appropriate directory (usually `public_html` or domain root)

2. **Configure Laravel on Hostinger**
   - Set document root to `/public` folder
   - Configure PHP version (8.1 or higher recommended)
   - Set up database in hPanel and update credentials

3. **Update Laravel `.env` on Server**
   ```env
   APP_URL=https://manite.site
   WHATSAPP_SERVICE_URL=https://whatsapp.manite.site
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

4. **Run Laravel Migrations**
   - Access terminal via hPanel or SSH
   - Navigate to Laravel root
   - Run: `php artisan migrate`
   - Run: `php artisan config:cache`
   - Run: `php artisan route:cache`

---

### Phase 2: Deploy Node.js WhatsApp Service (Subdomain)

#### 2.1: Create Subdomain in Hostinger

1. Go to **hPanel** → **Domains** → **Subdomains**
2. Click **"Create Subdomain"**
3. Enter subdomain name: `whatsapp`
4. Select your domain: `manite.site`
5. Result: `whatsapp.manite.site`
6. Click **Create**

#### 2.2: Create Node.js Application

1. In **hPanel**, go to **Advanced** → **Node.js**
2. Click **"Create Application"**
3. Configure:
   - **Application URL**: Select `whatsapp.manite.site`
   - **Application root**: Choose or create a folder (e.g., `whatsapp-service`)
   - **Node.js version**: Select latest stable (18.x or 20.x)
   - **Entry file**: `server.js`
   - **Application mode**: Production

4. Click **Create**

#### 2.3: Upload WhatsApp Service Files

Upload these files from your `whatsapp-service` folder to the Node.js app directory:

**Required Files:**
- `server.js`
- `package.json`
- `README.md`
- `.gitignore`
- `.env.example`

**DO NOT upload these:**
- `node_modules/` (will be installed on server)
- `.wwebjs_auth/` (sessions will be created on server)
- `.wwebjs_cache/`

#### 2.4: Configure Environment Variables

1. In your Node.js app settings in hPanel, find **Environment Variables**
2. Add this variable:
   ```
   LARAVEL_URL=https://manite.site
   ```

3. **Note**: `PORT` is automatically set by Hostinger - don't add it manually

#### 2.5: Install Dependencies

1. In hPanel Node.js app page, click **"Run npm install"** button
   
   OR if you have SSH access:
   ```bash
   cd /path/to/whatsapp-service
   npm install --production
   ```

#### 2.6: Start the Application

1. In hPanel Node.js app settings, click **"Start Application"**
2. The service should now be running at `https://whatsapp.manite.site`

#### 2.7: Verify Deployment

Test the service:
```bash
curl https://whatsapp.manite.site/api/status
```

Should return:
```json
{
  "success": true,
  "activeSessions": 0
}
```

---

### Phase 3: Connect Laravel to Node.js Service

#### 3.1: Update Laravel Configuration

Your Laravel `.env` should have:
```env
WHATSAPP_SERVICE_URL=https://whatsapp.manite.site
```

#### 3.2: Update Frontend Connection

Update any frontend code that connects to the WhatsApp service to use the subdomain URL instead of localhost.

Look for files that might have Socket.IO connections:
- Check blade templates for Socket.IO client code
- Update WebSocket connection URLs from `http://127.0.0.1:3000` to `https://whatsapp.manite.site`

---

### Phase 4: Testing

1. **Test Node.js Service Health**
   ```bash
   curl https://whatsapp.manite.site/api/status
   ```

2. **Test WhatsApp Connection**
   - Log into your Laravel app at `https://manite.site`
   - Navigate to WhatsApp settings
   - Try to connect WhatsApp (should show QR code)

3. **Test Message Flow**
   - Scan QR code
   - Send a test message
   - Verify AI responds

---

## Troubleshooting

### Issue: Node.js app won't start

**Solution:**
- Check hPanel logs for Node.js app
- Verify `package.json` has correct start script
- Ensure all dependencies are installed
- Check if port is already in use

### Issue: CORS errors

**Solution:**
- Verify `LARAVEL_URL` env var is set correctly in Node.js app
- Make sure Laravel URL doesn't have trailing slash
- Check if both services are using HTTPS in production

### Issue: WhatsApp sessions lost on restart

**Solution:**
- The `.wwebjs_auth` folder stores sessions
- Make sure this folder persists on the server
- Check folder permissions (needs write access)

### Issue: Socket.IO connection fails

**Solution:**
- Verify frontend is connecting to correct URL (whatsapp.manite.site)
- Check CORS settings in server.js
- Ensure both domains are using same protocol (HTTPS)

---

## Important Configuration Summary

### Entry File for Hostinger:
**`server.js`**

### Start Command (from package.json):
**`node server.js`**

### Environment Variables Required:
- **Node.js Service**: `LARAVEL_URL=https://manite.site`
- **Laravel**: `WHATSAPP_SERVICE_URL=https://whatsapp.manite.site`

### Dependencies (auto-installed):
- express
- socket.io
- whatsapp-web.js
- qrcode

---

## File Checklist for Upload

### Laravel App (manite.site):
- All Laravel files EXCEPT `whatsapp-service` folder
- Updated `.env` with production database and `WHATSAPP_SERVICE_URL`

### Node.js Service (whatsapp.manite.site):
- `server.js` ✓
- `package.json` ✓
- `README.md` ✓
- `.gitignore` ✓
- `.env.example` ✓

---

## Next Steps After Deployment

1. **Update Frontend Socket.IO URL**: Find and update any hardcoded `http://127.0.0.1:3000` references to `https://whatsapp.manite.site`

2. **Test All Features**: 
   - WhatsApp connection
   - Message sending/receiving
   - AI responses
   - Product landing pages

3. **Monitor Logs**: 
   - Laravel: Check `storage/logs/laravel.log`
   - Node.js: Check logs in hPanel Node.js app section

4. **Set Up SSL**: Ensure both domains have SSL certificates (Hostinger usually auto-provisions)

---

## Questions?

If you encounter issues:
1. Check the logs in hPanel
2. Verify environment variables are set correctly
3. Test each service independently
4. Ensure both services can communicate (no firewall blocking)
