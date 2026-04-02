# WhatsApp Service for ChatEasy

This is a Node.js service that handles WhatsApp Web connections using whatsapp-web.js.

## Deployment on Hostinger

### Prerequisites
- Node.js App hosting on Hostinger
- Subdomain created (e.g., whatsapp.manite.site)

### Setup Instructions

1. **Entry File**: `server.js`
2. **Start Command**: `node server.js` (or use the npm script: `npm start`)
3. **Port**: Hostinger assigns this automatically via `process.env.PORT`

### Environment Variables

Set these in Hostinger's hPanel under your Node.js App settings:

```
LARAVEL_URL=https://manite.site
```

### Local Development

1. Install dependencies:
   ```bash
   npm install
   ```

2. Create `.env` file:
   ```
   LARAVEL_URL=http://127.0.0.1:6500
   PORT=3000
   ```

3. Run the service:
   ```bash
   npm start
   ```

## API Endpoints

- `GET /api/status` - Check service status
- `POST /api/disconnect` - Disconnect a WhatsApp session

## Socket.IO Events

### Client → Server
- `init-whatsapp` - Initialize WhatsApp connection
- `send-message` - Send a message
- `get-chats` - Get chat list
- `get-messages` - Get messages from a chat
- `reconnect-session` - Reconnect existing session

### Server → Client
- `qr-code` - QR code for scanning
- `whatsapp-connected` - Connection successful
- `whatsapp-already-connected` - Already connected
- `new-message` - New message received
- `chats-list` - List of chats
- `messages-list` - List of messages
- `whatsapp-disconnected` - Disconnected event
- `whatsapp-error` - Error occurred
