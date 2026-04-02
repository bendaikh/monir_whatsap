# Hostinger Deployment - Common Issues & Solutions

## Node.js Service Issues

### ❌ Problem: "Application failed to start"

**Check:**
1. Verify Entry file is exactly: `server.js` (case-sensitive)
2. Check hPanel logs for specific error messages
3. Ensure `package.json` exists and is valid JSON

**Solution:**
```bash
# In hPanel Node.js terminal or SSH:
cd /path/to/whatsapp-service
node server.js
# Check what error appears
```

---

### ❌ Problem: "Cannot find module 'express'" or similar

**Check:**
- Dependencies not installed

**Solution:**
- In hPanel → Node.js App → Click "Run npm install"
- Or via SSH: `cd /path/to/whatsapp-service && npm install`

---

### ❌ Problem: "Port already in use"

**Check:**
- Hostinger automatically assigns PORT via `process.env.PORT`
- Don't manually set PORT in environment variables

**Solution:**
- Remove any manual PORT setting in hPanel environment variables
- Hostinger manages the port automatically

---

### ❌ Problem: "/api/status returns 502 Bad Gateway"

**Check:**
- Node.js application is not running
- Application crashed after starting

**Solution:**
1. Go to hPanel → Node.js App
2. Check if status shows "Running"
3. If not, click "Start Application"
4. Check logs for crash reasons
5. Common crash reasons:
   - Missing dependencies
   - Syntax errors in server.js
   - Permission issues with .wwebjs_auth folder

---

## Laravel Issues

### ❌ Problem: "SQLSTATE[HY000] [1045] Access denied"

**Check:**
- Database credentials in `.env` are incorrect

**Solution:**
1. Go to hPanel → Databases
2. Verify database name, username, password
3. Update Laravel `.env` file
4. Run: `php artisan config:clear`

---

### ❌ Problem: "500 Internal Server Error"

**Check:**
1. Laravel logs: `storage/logs/laravel.log`
2. PHP error logs in hPanel

**Solution:**
- Fix the specific error shown in logs
- Common causes:
  - Missing APP_KEY (run: `php artisan key:generate`)
  - Permission issues on storage/ folder
  - Missing migrations (run: `php artisan migrate`)

---

## Communication Issues

### ❌ Problem: "Failed to connect to WhatsApp service"

**Check:**
1. Is Node.js service running?
   ```bash
   curl https://whatsapp.manite.site/api/status
   ```
2. Is `WHATSAPP_SERVICE_URL` set correctly in Laravel `.env`?
3. Browser console errors?

**Solution:**
- Verify both services are using HTTPS
- Check CORS configuration in `server.js`
- Ensure `LARAVEL_URL` is set in Node.js environment

---

### ❌ Problem: "CORS policy blocked"

**Error Message:**
```
Access to XMLHttpRequest at 'https://whatsapp.manite.site' from origin 'https://manite.site' 
has been blocked by CORS policy
```

**Solution:**
Update CORS in `server.js` to match your domain:

```javascript
const io = new Server(server, {
    cors: {
        origin: process.env.LARAVEL_URL || "http://127.0.0.1:6500",
        methods: ["GET", "POST"],
        credentials: true
    }
});
```

Then restart Node.js app in hPanel.

---

### ❌ Problem: "WebSocket connection failed"

**Check:**
- Mixed content (HTTP/HTTPS)
- Firewall blocking WebSocket connections
- SSL certificate issues

**Solution:**
1. Ensure both domains have valid SSL certificates
2. Use `wss://` (secure WebSocket) in production
3. Check browser console for specific error
4. Verify Socket.IO client version matches server version

---

## WhatsApp Specific Issues

### ❌ Problem: "QR code doesn't appear"

**Check:**
1. Node.js service is running
2. Socket.IO connection successful
3. Browser console for errors

**Solution:**
```javascript
// In browser console, check:
console.log(this.socket.connected); // Should be true
```

If false:
- Check `WHATSAPP_SERVICE_URL` in Laravel `.env`
- Verify Node.js service is accessible
- Check CORS settings

---

### ❌ Problem: "Sessions lost after restart"

**Check:**
- `.wwebjs_auth` folder permissions
- Folder persists after restart

**Solution:**
1. SSH into server
2. Check folder exists: `ls -la /path/to/whatsapp-service/.wwebjs_auth/`
3. Set permissions: `chmod -R 755 .wwebjs_auth`
4. Ensure folder is not being deleted on restart

---

### ❌ Problem: "Protocol error (Target.createBrowserContext): Browser closed"

**Check:**
- Puppeteer/Chrome dependencies missing
- Memory limits

**Solution:**
1. Verify Puppeteer dependencies are installed
2. On Linux servers, may need:
   ```bash
   sudo apt-get install -y chromium-browser
   ```
3. Contact Hostinger support - they may need to install Chrome dependencies
4. Alternative: Ask about Puppeteer compatibility on Cloud Startup plan

---

## Performance Issues

### ❌ Problem: "Service is slow or timing out"

**Check:**
- Multiple WhatsApp sessions active
- Memory usage
- CPU usage

**Solution:**
1. Limit concurrent WhatsApp connections
2. Implement session cleanup for inactive sessions
3. Monitor resource usage in hPanel
4. Consider upgrading hosting plan if needed

---

## Deployment Best Practices

### ✅ Use Environment Variables
- Never hardcode URLs
- Use `process.env.LARAVEL_URL` in Node.js
- Use `env('WHATSAPP_SERVICE_URL')` in Laravel

### ✅ Enable Logging
- Check logs regularly in hPanel
- Add console.log statements for debugging
- Monitor Laravel logs in `storage/logs/`

### ✅ Test Before Going Live
1. Test `/api/status` endpoint
2. Test Socket.IO connection
3. Test WhatsApp QR code generation
4. Test message sending/receiving
5. Test AI auto-reply

### ✅ Security
- Use HTTPS for all production URLs
- Don't expose sensitive data in logs
- Keep dependencies updated
- Implement rate limiting for API endpoints

---

## Quick Diagnostic Commands

### Check Node.js Service Status
```bash
curl https://whatsapp.manite.site/api/status
```

### Check Laravel is Running
```bash
curl https://manite.site
```

### Check Socket.IO Connection (Browser Console)
```javascript
const socket = io('https://whatsapp.manite.site');
socket.on('connect', () => console.log('Connected!'));
socket.on('connect_error', (err) => console.error('Error:', err));
```

### View Node.js Logs
- Go to hPanel → Node.js → Your App → Logs

### View Laravel Logs
- FTP/File Manager → `storage/logs/laravel.log`
- Or via SSH: `tail -f storage/logs/laravel.log`

---

## Getting Help

### Information to Provide to Hostinger Support

1. **Plan**: Cloud Startup
2. **Node.js App Details**:
   - Entry file: `server.js`
   - Start command: `node server.js`
   - Package.json start script: `"start": "node server.js"`
   - Node version: 18.x or 20.x
3. **Error messages** from hPanel logs
4. **What you've tried** so far

### Share Your Configuration

**package.json:**
```json
{
  "name": "whatsapp-service",
  "version": "1.0.0",
  "main": "server.js",
  "scripts": {
    "start": "node server.js"
  },
  "dependencies": {
    "express": "^4.18.2",
    "qrcode": "^1.5.3",
    "socket.io": "^4.6.1",
    "whatsapp-web.js": "^1.23.0"
  }
}
```

**Entry point:** `server.js`

**Port configuration:** Uses `process.env.PORT` (Hostinger auto-assigns)

---

## Contact Points

- **Hostinger Support**: Via hPanel chat/ticket system
- **Check Hostinger Documentation**: Search for "Node.js App" in their knowledge base
- **Server Requirements**: Verify Cloud Startup plan supports Node.js apps

---

## Success Indicators

When everything is working correctly:

✅ `https://manite.site` loads Laravel app  
✅ `https://whatsapp.manite.site/api/status` returns JSON with success: true  
✅ Browser can connect to Socket.IO at whatsapp.manite.site  
✅ QR code appears when trying to connect WhatsApp  
✅ Messages flow between WhatsApp and Laravel  
✅ AI responses are sent automatically  
