# Quick Deployment Checklist

## Before You Start
- [ ] Hostinger Cloud Startup plan active
- [ ] Domain manite.site pointing to Hostinger
- [ ] Database created in hPanel

---

## Deployment Steps

### 1. Create Subdomain
- [ ] Go to hPanel → Domains → Subdomains
- [ ] Create: `whatsapp.manite.site`

### 2. Deploy Laravel (manite.site)
- [ ] Upload all files EXCEPT `whatsapp-service` folder via FTP/File Manager
- [ ] Set document root to `/public`
- [ ] Create database in hPanel
- [ ] Update `.env` file on server:
  ```
  APP_URL=https://manite.site
  WHATSAPP_SERVICE_URL=https://whatsapp.manite.site
  DB_HOST=localhost
  DB_DATABASE=your_db_name
  DB_USERNAME=your_db_user
  DB_PASSWORD=your_db_pass
  ```
- [ ] Run: `php artisan migrate`
- [ ] Run: `php artisan config:cache`

### 3. Deploy Node.js Service (whatsapp.manite.site)
- [ ] Go to hPanel → Advanced → Node.js
- [ ] Click "Create Application"
- [ ] Configure:
  - **URL**: whatsapp.manite.site
  - **Node version**: 18.x or 20.x
  - **Entry file**: `server.js`
  - **Mode**: Production
- [ ] Upload these files to the Node.js app directory:
  - `server.js`
  - `package.json`
  - `README.md`
  - `.gitignore`
  - `.env.example`
- [ ] Set environment variable in hPanel:
  - `LARAVEL_URL=https://manite.site`
- [ ] Click "Run npm install" in hPanel
- [ ] Click "Start Application"

### 4. Verify Deployment
- [ ] Test Laravel: Visit `https://manite.site`
- [ ] Test Node.js: Visit `https://whatsapp.manite.site/api/status`
  - Should return: `{"success":true,"activeSessions":0}`
- [ ] Test WhatsApp connection: Login to Laravel app and try connecting WhatsApp

---

## Important Information for Hostinger Support

**Entry File**: `server.js`

**Start Command**: `node server.js` (or `npm start`)

**Package.json Start Script**:
```json
"scripts": {
  "start": "node server.js"
}
```

**Port**: Automatically assigned by Hostinger via `process.env.PORT`

**Dependencies** (auto-installed via npm install):
- express: ^4.18.2
- socket.io: ^4.6.1
- whatsapp-web.js: ^1.23.0
- qrcode: ^1.5.3

---

## After Deployment

### Test the Integration
1. Visit `https://manite.site` and login
2. Go to WhatsApp settings
3. Click "Connect WhatsApp"
4. QR code should appear
5. Scan with WhatsApp app
6. Test sending/receiving messages
7. Test AI auto-reply

### Monitor Logs
- **Laravel logs**: Check `storage/logs/laravel.log` via FTP
- **Node.js logs**: Check logs section in hPanel Node.js app

---

## Troubleshooting

### Node.js service won't start
- Check hPanel logs for errors
- Verify all dependencies installed (`npm install`)
- Ensure `server.js` has no syntax errors
- Check if port is available

### CORS errors
- Verify `LARAVEL_URL` is set correctly in Node.js app
- Ensure both URLs use HTTPS (not mixed HTTP/HTTPS)
- Check that Laravel URL doesn't have trailing slash

### WhatsApp won't connect
- Verify Node.js service is running: `https://whatsapp.manite.site/api/status`
- Check browser console for connection errors
- Verify Socket.IO can connect (network tab in browser dev tools)
- Check that frontend is using correct WHATSAPP_SERVICE_URL

### Sessions not persisting
- Ensure `.wwebjs_auth` folder exists and is writable
- Check folder permissions on server
- Node.js app needs write access to its directory

---

## Environment Variables Summary

### Laravel (.env):
```env
APP_URL=https://manite.site
WHATSAPP_SERVICE_URL=https://whatsapp.manite.site
```

### Node.js (.env in hPanel):
```env
LARAVEL_URL=https://manite.site
```

---

## Support Information

If Hostinger support asks for configuration details, share:

1. **Entry file**: `server.js`
2. **Start command**: `node server.js`
3. **Port**: Uses `process.env.PORT` (Hostinger assigns automatically)
4. **Node version**: 18.x or 20.x (latest LTS)
5. **Package manager**: npm
6. **Dependencies**: Listed in package.json

Share your `package.json` file if they need to see dependencies.
