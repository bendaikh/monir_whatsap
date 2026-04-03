# FIX: "Not Found" Error on Hostinger

## Your Current Situation
- Domain: mediumaquamarine-gazelle-424101.hostingersite.com
- Issue: Seeing "Not Found" page
- Files uploaded to: `nodejs` folder
- .env configured correctly ✅

## The Problem
The Node.js app is uploaded but NOT CONFIGURED in Hostinger's Node.js Application Manager.

## The Solution - Follow These Exact Steps

### Step 1: Access Hostinger Control Panel (hPanel)
1. Go to https://hpanel.hostinger.com
2. Log in with your Hostinger credentials
3. Select your hosting plan for `mediumaquamarine-gazelle-424101.hostingersite.com`

### Step 2: Find Node.js Application Manager
Look for ONE of these menu options (depends on your Hostinger plan):
- **Advanced** → **Node.js**
- **Advanced** → **Node.js Applications**
- **Advanced** → **Node.js Selector**
- **Website** → **Node.js**

**⚠️ IMPORTANT**: If you DON'T see any Node.js option, your current hosting plan doesn't support Node.js apps. You'll need to either:
- Upgrade to a plan that supports Node.js (Business or higher)
- Deploy the Node.js service elsewhere (see Alternative Options below)

### Step 3: Create/Configure Node.js Application

If you see a Node.js application menu:

#### If NO Application Exists Yet:
1. Click **"Create Application"** or **"Add New Application"**
2. Fill in these details:

   **Application root**: `/nodejs` (or `/domains/mediumaquamarine-gazelle-424101.hostingersite.com/nodejs`)
   
   **Application URL**: `https://mediumaquamarine-gazelle-424101.hostingersite.com`
   
   **Application startup file**: `server.js`
   
   **Node.js version**: Select `18.x` or `20.x` (LTS version)
   
   **Application mode**: `production`

3. Click **"Create"** or **"Save"**

#### If Application Already Exists:
1. Find your application in the list
2. Click **"Edit"** or click on the application name
3. Verify these settings:
   - **Entry Point**: `server.js` ✅
   - **Application Root**: Points to your `nodejs` folder ✅
   - **Status**: Should be "Running" (if not, continue to next steps)

### Step 4: Verify Files Are in Correct Location

In **File Manager** (Files → File Manager), you should have this structure:

```
/domains/mediumaquamarine-gazelle-424101.hostingersite.com/
├── public_html/              ← Your Laravel app files
│   ├── index.php
│   ├── .htaccess
│   └── ...
│
└── nodejs/                   ← Your Node.js WhatsApp service
    ├── server.js            ✅ MUST be here
    ├── package.json         ✅ MUST be here
    ├── .env                 ✅ MUST be here
    └── .gitignore           ✅ MUST be here
```

**If your files are in the wrong location:**
1. Create a `nodejs` folder at the same level as `public_html`
2. Move `server.js`, `package.json`, `.env`, `.gitignore` into the `nodejs` folder
3. Update the "Application Root" in Node.js Application settings

### Step 5: Install Dependencies

In the Node.js Application panel:
1. Look for **"Run npm install"** button or **"Install Dependencies"**
2. Click it and wait (may take 2-5 minutes)
3. You should see success message or logs showing packages installed

**Packages that should be installed:**
- express
- socket.io
- qrcode
- whatsapp-web.js

### Step 6: Set Environment Variables (Optional but Recommended)

In the Node.js Application settings:
1. Look for "Environment Variables" section
2. Add a new variable:
   - **Name**: `LARAVEL_URL`
   - **Value**: `https://mediumaquamarine-gazelle-424101.hostingersite.com`

(The PORT variable is automatically set by Hostinger, don't add it)

### Step 7: Start the Application

1. In Node.js Application panel, look for **"Start"** or **"Restart"** button
2. Click it
3. Wait 10-20 seconds
4. Status should change to **"Running"** ✅

### Step 8: Test the Service

Open your browser and visit:
```
https://mediumaquamarine-gazelle-424101.hostingersite.com/api/status
```

**Expected Result:**
```json
{
  "success": true,
  "activeSessions": 0
}
```

**If you still see "Not Found":**
- Check Application Logs in hPanel
- See troubleshooting section below

---

## Troubleshooting

### Issue 1: No Node.js Option in hPanel Menu

**Cause**: Your hosting plan doesn't support Node.js

**Solutions**:
1. **Upgrade your Hostinger plan** to Business or Premium
2. **Use Alternative Deployment** (see below)

### Issue 2: Application Status Shows "Stopped"

**Cause**: Application crashed or failed to start

**Solutions**:
1. Click "View Logs" or "Application Logs" in hPanel
2. Look for error messages
3. Common issues:
   - Missing dependencies → Run "npm install" again
   - Wrong entry point → Verify `server.js` is the startup file
   - Port conflict → Hostinger should handle this automatically

### Issue 3: npm install Fails

**Cause**: Dependencies can't be installed

**Solutions**:
1. Check if `package.json` is in the correct location
2. Try clicking "npm install" again
3. Check Node.js version is compatible (18.x or 20.x)
4. Contact Hostinger support if persistent

### Issue 4: Application Runs but Still "Not Found"

**Cause**: Reverse proxy not configured or wrong URL path

**Solutions**:
1. Try accessing: `https://yourdomain.com:PORT/api/status` (check what PORT was assigned)
2. Verify Application URL matches your domain exactly
3. Check if Hostinger set up reverse proxy correctly
4. Contact Hostinger support to verify routing

### Issue 5: Puppeteer/Chrome Errors in Logs

**Cause**: Missing system libraries for Chromium/Puppeteer

**Example Error:**
```
Error: Failed to launch the browser process!
/usr/bin/chromium: error while loading shared libraries
```

**Solutions**:
1. **Contact Hostinger Support** immediately
2. Ask them to install these system packages:
   - chromium
   - chromium-browser
   - libatk-bridge2.0-0
   - libgtk-3-0
   - libnss3
   - libxss1
   - libasound2
   - libgbm1

3. If they can't install these on shared hosting, you MUST use alternative deployment

---

## Alternative Deployment Options

If Hostinger doesn't support Node.js or Puppeteer properly, deploy the WhatsApp service separately:

### Option A: Railway.app (Recommended - Free Tier Available)

1. Go to https://railway.app
2. Sign up with GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Select/upload your `whatsapp-service` folder
5. Railway will auto-detect Node.js and deploy
6. Get your Railway URL (e.g., `https://whatsapp-service-production-xxxx.railway.app`)
7. Update your Laravel `.env`:
   ```
   WHATSAPP_SERVICE_URL=https://whatsapp-service-production-xxxx.railway.app
   ```

### Option B: Render.com (Free Tier)

1. Go to https://render.com
2. Create account
3. New → Web Service
4. Connect your repo or upload files
5. Settings:
   - **Build Command**: `npm install`
   - **Start Command**: `npm start`
6. Deploy
7. Get your Render URL and update Laravel `.env`

### Option C: Keep Laravel on Hostinger, WhatsApp Service on Railway

This is actually the RECOMMENDED setup because:
- ✅ Puppeteer/Chrome works reliably on Railway
- ✅ Free tier available
- ✅ Automatic deployments
- ✅ Better logging and monitoring
- ✅ Laravel stays on Hostinger (no migration needed)

**Setup:**
1. Deploy Node.js service to Railway (5 minutes)
2. Get Railway URL: `https://your-app.railway.app`
3. Update Laravel `.env` on Hostinger:
   ```
   WHATSAPP_SERVICE_URL=https://your-app.railway.app
   ```
4. Update WhatsApp service `.env` on Railway:
   ```
   LARAVEL_URL=https://mediumaquamarine-gazelle-424101.hostingersite.com
   ```

---

## Quick Checklist

Before asking for help, verify:

- [ ] Node.js Application created in hPanel
- [ ] Files are in `/nodejs` or correct application root
- [ ] `server.js` is set as startup/entry file
- [ ] `npm install` completed successfully
- [ ] Application status shows "Running"
- [ ] No errors in Application Logs
- [ ] Node.js version is 18.x or 20.x
- [ ] `.env` file has correct `LARAVEL_URL`

---

## Need More Help?

1. **Check Application Logs** in hPanel first (most issues show clear error messages)
2. **Contact Hostinger Support** via live chat - they can see your server configuration
3. **Consider Alternative Deployment** if Hostinger limitations persist

---

## Summary

The "Not Found" error means either:
1. ❌ Node.js application not configured in hPanel → Follow Step 3 above
2. ❌ Application stopped/crashed → Check logs and restart
3. ❌ Files in wrong location → Move to `/nodejs` folder
4. ❌ Hostinger plan doesn't support Node.js → Upgrade or use Railway

**Most likely cause**: You uploaded files but didn't configure the Node.js Application in hPanel. Follow Step 2-7 above to fix this.
