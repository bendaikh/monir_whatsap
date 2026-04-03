# WhatsApp Service for ChatEasy

Node.js service that provides WhatsApp Web integration for the ChatEasy Laravel application.

## Quick Hostinger Deployment

### What You Need
1. Hostinger account with Node.js support
2. Domain: `mediumaquamarine-gazelle-424101.hostingersite.com`
3. These files (already in `hostinger-deploy.zip`)

### Deployment Steps

#### 1. Access Hostinger hPanel
- Log in to your Hostinger account
- Go to your hosting control panel (hPanel)

#### 2. Set Up Node.js Application
- Navigate to **Advanced** → **Node.js Applications** (or **Node.js Selector**)
- Click **Create Application** or **Add Application**
- Configure:
  - **Application Root**: `/nodejs` or `/domains/yourdomain.com/nodejs`
  - **Application URL**: Your domain URL
  - **Node.js Version**: 18.x or 20.x (LTS)
  - **Application Mode**: Production
  - **Application Startup File**: `server.js`

#### 3. Upload Files
- Go to **Files** → **File Manager**
- Navigate to the `nodejs` folder (create it if it doesn't exist)
- Upload these files:
  - ✅ `server.js`
  - ✅ `package.json`
  - ✅ `.env`
  - ✅ `.gitignore`
  - ✅ `README.md`
  
- **DO NOT upload:**
  - ❌ `node_modules/` (too large, Hostinger installs them)
  - ❌ `.wwebjs_auth/` (local session data)
  - ❌ `.wwebjs_cache/` (local cache)

#### 4. Configure Environment Variables
In Node.js Application settings, add:
- **Key**: `LARAVEL_URL`
- **Value**: `https://mediumaquamarine-gazelle-424101.hostingersite.com`

(PORT is auto-set by Hostinger)

#### 5. Install Dependencies
- In the Node.js Application panel, click **"Run npm install"**
- Wait for completion (may take 2-3 minutes)

#### 6. Start Application
- Click **"Start Application"** or **"Restart Application"**
- Check status - it should show "Running"

#### 7. Test the Service
Open in browser:
```
https://mediumaquamarine-gazelle-424101.hostingersite.com/api/status
```

Expected response:
```json
{
  "success": true,
  "activeSessions": 0
}
```

## How It Works

This service:
1. Runs a Node.js/Express server with Socket.IO
2. Uses `whatsapp-web.js` to connect to WhatsApp Web
3. Communicates with your Laravel app via webhooks and Socket.IO
4. Manages WhatsApp sessions for multiple users

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `LARAVEL_URL` | Your Laravel app URL | `http://127.0.0.1:6500` |
| `PORT` | Port to run on (auto-set by Hostinger) | `3000` |

## API Endpoints

### `GET /api/status`
Check service status
```bash
curl https://yourdomain.com/api/status
```

### `POST /api/disconnect`
Disconnect a WhatsApp session
```bash
curl -X POST https://yourdomain.com/api/disconnect \
  -H "Content-Type: application/json" \
  -d '{"sessionId": "user_123"}'
```

## Socket.IO Events

### Client → Server
- `init-whatsapp` - Initialize WhatsApp connection
- `send-message` - Send a WhatsApp message
- `get-chats` - Get chat list
- `get-messages` - Get messages from a chat
- `reconnect-session` - Reconnect existing session
- `disconnect` - Disconnect from Socket.IO

### Server → Client
- `qr-code` - QR code for scanning
- `whatsapp-connected` - WhatsApp successfully connected
- `whatsapp-already-connected` - Session already active
- `whatsapp-disconnected` - WhatsApp disconnected
- `whatsapp-error` - Error occurred
- `new-message` - New message received
- `chats-list` - List of chats
- `messages-list` - List of messages
- `message-sent` - Confirmation message was sent
- `error` - General error

## Troubleshooting

### "Not Found" Error
**Problem**: You see a 404 Not Found page

**Solutions**:
1. Check Node.js Application status in hPanel - should be "Running"
2. Verify Entry Point is set to `server.js`
3. Check Application Root path is correct
4. Review application logs in hPanel

### Application Won't Start
**Problem**: Application status shows "Stopped" or crashes immediately

**Solutions**:
1. Check application logs in hPanel for errors
2. Verify all dependencies installed correctly
3. Run `npm install` again from hPanel
4. Check if Hostinger has required system libraries

### Puppeteer/Chrome Errors
**Problem**: Errors about missing Chrome or Chromium libraries

**Solutions**:
1. Contact Hostinger support - they may need to install system packages
2. Request these packages be installed:
   - `chromium`
   - `libatk-bridge2.0-0`
   - `libgtk-3-0`
   - `libnss3`
   - `libxss1`
   - `libasound2`
3. Consider upgrading to VPS hosting for full control
4. Alternative: Deploy Node.js service to Railway/Render (free tier)

### Can't Connect to Laravel App
**Problem**: WhatsApp connects but doesn't communicate with Laravel

**Solutions**:
1. Verify `LARAVEL_URL` environment variable is correct
2. Check Laravel app is accessible at that URL
3. Ensure Laravel webhook route is working: `/webhook/whatsapp`
4. Check CORS settings in `server.js`

### Socket.IO Connection Issues
**Problem**: Frontend can't connect to Socket.IO

**Solutions**:
1. Check if Hostinger properly routes Socket.IO traffic
2. Verify CORS origin matches your Laravel URL
3. Test Socket.IO endpoint directly
4. Check browser console for connection errors

## Alternative Deployment Options

If Hostinger shared hosting doesn't support this well (due to Puppeteer requirements), consider:

### Option 1: Railway.app (Recommended for Free Tier)
```bash
# Install Railway CLI
npm install -g @railway/cli

# Deploy
railway login
railway init
railway up
```

### Option 2: Render.com
1. Connect GitHub repo
2. Select "Web Service"
3. Build command: `npm install`
4. Start command: `npm start`

### Option 3: Heroku
```bash
heroku create your-whatsapp-service
git push heroku main
```

Then update Laravel `.env`:
```
WHATSAPP_SERVICE_URL=https://your-service.railway.app
```

## Technical Details

### Dependencies
- **express**: Web server framework
- **socket.io**: Real-time bidirectional communication
- **whatsapp-web.js**: WhatsApp Web API wrapper
- **qrcode**: QR code generation for WhatsApp auth

### Port Configuration
The service binds to `0.0.0.0` on the port specified by `PORT` environment variable (or 3000 default). Hostinger automatically assigns and manages the port.

### Session Storage
WhatsApp sessions are stored in `.wwebjs_auth/` directory using LocalAuth. This persists login state across restarts.

### Security Notes
- Service runs on internal port, exposed via Hostinger's reverse proxy
- CORS restricted to `LARAVEL_URL` domain
- No authentication on endpoints - should be behind firewall or add auth

## Support

For issues:
1. Check application logs in Hostinger hPanel
2. Review Laravel logs: `storage/logs/laravel.log`
3. Test endpoints individually with curl/Postman
4. Contact Hostinger support for infrastructure issues

## Development vs Production

### Development (Local)
```bash
# Install dependencies
npm install

# Create .env file
LARAVEL_URL=http://127.0.0.1:6500
PORT=3000

# Run
npm start
```

### Production (Hostinger)
- Uses environment variables from hPanel
- Runs in production mode
- Managed by Hostinger's Node.js system
- Auto-restarts on crashes

## File Structure
```
whatsapp-service/
├── server.js              # Main application
├── package.json           # Dependencies
├── .env                   # Environment config
├── .gitignore            # Git ignore rules
├── README.md             # This file
├── .wwebjs_auth/         # Session storage (auto-created)
└── .wwebjs_cache/        # WhatsApp cache (auto-created)
```

## License
Part of ChatEasy application
