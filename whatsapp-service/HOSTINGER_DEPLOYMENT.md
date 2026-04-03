# Hostinger Node.js Deployment Guide

## Prerequisites
Your Hostinger plan must support Node.js applications.

## Deployment Steps

### Step 1: Prepare the Application
The following files are required in your upload:
- `server.js` (main application file)
- `package.json` (dependencies)
- `.env` (environment configuration)
- `.gitignore`

### Step 2: Configure Hostinger Node.js Application

1. **Log into Hostinger hPanel**
2. **Navigate to Advanced → Node.js Applications** (or similar menu)
3. **Create a New Node.js Application:**
   - **Application Root**: `/nodejs` (or `/public_html/nodejs`)
   - **Application URL**: `https://mediumaquamarine-gazelle-424101.hostingersite.com`
   - **Node.js Version**: Select latest LTS (18.x or 20.x)
   - **Application Mode**: Production
   - **Entry Point**: `server.js`

### Step 3: Upload Files via File Manager

1. Go to **Files → File Manager** in hPanel
2. Navigate to the `nodejs` folder (or create it if it doesn't exist)
3. Upload ALL files from the `whatsapp-service` folder:
   - `server.js`
   - `package.json`
   - `.env`
   - `.gitignore`
   
   **DO NOT UPLOAD:**
   - `node_modules/` folder (Hostinger will install these)
   - `.wwebjs_auth/` folder
   - `.wwebjs_cache/` folder
   - `whatsapp-service.zip`

### Step 4: Install Dependencies

In the Node.js Application settings in hPanel:
1. Click **"Run NPM Install"** or similar button
2. Wait for dependencies to install
3. You should see `express`, `socket.io`, `qrcode`, and `whatsapp-web.js` installed

### Step 5: Set Environment Variables (Important!)

In the Node.js Application settings, add environment variables:
- `LARAVEL_URL`: `https://mediumaquamarine-gazelle-424101.hostingersite.com`
- `PORT`: Leave this - Hostinger auto-assigns it

### Step 6: Start the Application

1. In Node.js Application settings, click **"Start Application"** or **"Restart Application"**
2. The application should start running

### Step 7: Verify Deployment

Access: `https://mediumaquamarine-gazelle-424101.hostingersite.com/api/status`

You should see:
```json
{
  "success": true,
  "activeSessions": 0
}
```

## Important Notes for Hostinger

### File Structure Should Be:
```
/home/u15868xxxx/domains/mediumaquamarine-gazelle-424101.hostingersite.com/
├── public_html/          (Your Laravel app)
│   ├── index.php
│   ├── .htaccess
│   └── ...
└── nodejs/               (Your Node.js WhatsApp service)
    ├── server.js
    ├── package.json
    ├── .env
    └── .gitignore
```

### Port Configuration
- Hostinger automatically assigns a port via the `PORT` environment variable
- Your application code already handles this: `const PORT = process.env.PORT || 3000`
- Hostinger's reverse proxy will route requests to your assigned port

### Reverse Proxy
Hostinger automatically sets up a reverse proxy so:
- `https://yourdomain.com/` → Laravel (public_html)
- `https://yourdomain.com/socket.io/` → Node.js app
- `https://yourdomain.com/api/status` → Node.js app

### Puppeteer/Chrome Dependencies
The WhatsApp service uses Puppeteer (chromium). Hostinger may not have all required libraries. If you get errors about missing Chrome dependencies:

1. Contact Hostinger support to install system packages:
   - `chromium`
   - `libatk-bridge2.0-0`
   - `libgtk-3-0`
   - `libnss3`
   - `libxss1`
   - `libasound2`

2. Or switch to a VPS plan where you have full control

## Troubleshooting

### Issue: "Not Found" Error
**Cause**: Node.js app not properly configured or not running
**Solution**: 
- Check Node.js Application status in hPanel
- Ensure Entry Point is set to `server.js`
- Check application logs in hPanel

### Issue: Application Crashes on Start
**Cause**: Missing dependencies or Chrome/Puppeteer libraries
**Solution**:
- Run NPM Install again
- Check error logs in hPanel
- Contact Hostinger support about Puppeteer requirements

### Issue: Can't Connect to Laravel
**Cause**: Wrong `LARAVEL_URL` in environment variables
**Solution**: 
- Verify `LARAVEL_URL` is set to `https://mediumaquamarine-gazelle-424101.hostingersite.com`
- No trailing slash

### Issue: Socket.IO Connection Fails
**Cause**: CORS or reverse proxy issues
**Solution**:
- Ensure Laravel app is on same domain
- Check Socket.IO CORS configuration in `server.js`

## Alternative: Use External VPS for WhatsApp Service

If Hostinger shared hosting doesn't support Puppeteer/Chrome properly, consider:

1. **Deploy WhatsApp Service to Railway.app (Free tier)**
2. **Deploy WhatsApp Service to Render.com (Free tier)**
3. **Deploy WhatsApp Service to Heroku**
4. **Deploy to DigitalOcean App Platform**

Then update your Laravel `.env`:
```
WHATSAPP_SERVICE_URL=https://your-whatsapp-service.railway.app
```

This is often more reliable for Puppeteer-based applications.
