# Deployment Architecture

## Current Setup (Local Development)

```
┌─────────────────────────────────────────────┐
│  Local Development Environment              │
├─────────────────────────────────────────────┤
│                                             │
│  Laravel App (Port 6500)                    │
│  http://127.0.0.1:6500                      │
│         ↕                                   │
│  Node.js WhatsApp Service (Port 3000)       │
│  http://127.0.0.1:3000                      │
│                                             │
└─────────────────────────────────────────────┘
```

## Production Setup (Hostinger)

```
┌──────────────────────────────────────────────────────────┐
│  Hostinger Cloud Startup                                 │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌─────────────────────────────────────────────┐        │
│  │  Main Domain: manite.site                   │        │
│  │  ────────────────────────────────            │        │
│  │  • Laravel Application (PHP)                │        │
│  │  • Document root: /public                   │        │
│  │  • MySQL Database                           │        │
│  │  • ENV: WHATSAPP_SERVICE_URL=               │        │
│  │         https://whatsapp.manite.site        │        │
│  └─────────────────────────────────────────────┘        │
│                       ↕                                  │
│                   HTTPS API                              │
│                       ↕                                  │
│  ┌─────────────────────────────────────────────┐        │
│  │  Subdomain: whatsapp.manite.site            │        │
│  │  ────────────────────────────────            │        │
│  │  • Node.js Application                      │        │
│  │  • Entry: server.js                         │        │
│  │  • Auto-assigned PORT                       │        │
│  │  • Socket.IO + Express                      │        │
│  │  • ENV: LARAVEL_URL=                        │        │
│  │         https://manite.site                 │        │
│  └─────────────────────────────────────────────┘        │
│                                                          │
└──────────────────────────────────────────────────────────┘
                       ↕
                Browser/WhatsApp
```

## Communication Flow

### 1. User Opens Laravel App
```
Browser → https://manite.site → Laravel serves page
```

### 2. WhatsApp Connection
```
Browser → Socket.IO connect → wss://whatsapp.manite.site
        → Node.js service → WhatsApp Web API → QR Code
        → Browser shows QR
```

### 3. Incoming WhatsApp Message
```
WhatsApp → Node.js service → POST https://manite.site/api/whatsapp/process-message
        → Laravel processes with AI → Returns response
        → Node.js sends reply via WhatsApp
        → Socket.IO notifies browser
```

## File Structure

### On Hostinger:

```
📁 manite.site (Laravel App Directory)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Document Root
├── resources/
├── routes/
├── storage/
├── .env            ← Set WHATSAPP_SERVICE_URL
└── ...

📁 whatsapp-service (Node.js App Directory - Separate!)
├── server.js       ← Entry file
├── package.json
├── .env            ← Set LARAVEL_URL via hPanel
├── .wwebjs_auth/   ← Created automatically
├── .wwebjs_cache/  ← Created automatically
└── node_modules/   ← Created by npm install
```

## Key Points

1. **Two Separate Applications**: Laravel and Node.js run as independent apps
2. **Different Domains**: Main domain for Laravel, subdomain for Node.js
3. **HTTPS Communication**: They communicate via HTTPS REST API and WebSockets
4. **Environment Variables**: Each app has its own environment configuration
5. **No Shared Filesystem**: They don't share files - they communicate over network

## URLs to Remember

| Service | Development | Production |
|---------|-------------|------------|
| Laravel | http://127.0.0.1:6500 | https://manite.site |
| Node.js | http://127.0.0.1:3000 | https://whatsapp.manite.site |
| Socket.IO | ws://localhost:3000 | wss://whatsapp.manite.site |
