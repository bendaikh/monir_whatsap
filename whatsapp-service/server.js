const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const qrcode = require('qrcode');
const { execSync } = require('child_process');
const fs = require('fs');

// Helper function to find Chromium executable
function findChromiumPath() {
    const isWindows = process.platform === 'win32';
    
    let possiblePaths = [
        process.env.PUPPETEER_EXECUTABLE_PATH,
    ];
    
    if (isWindows) {
        // Windows paths
        possiblePaths.push(
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            process.env.LOCALAPPDATA + '\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files\\BraveSoftware\\Brave-Browser\\Application\\brave.exe',
            'C:\\Program Files (x86)\\BraveSoftware\\Brave-Browser\\Application\\brave.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe'
        );
    } else {
        // Linux/Unix paths
        possiblePaths.push(
            '/usr/bin/google-chrome-stable',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/nix/var/nix/profiles/default/bin/chromium'
        );
        
        // Try to find chromium using 'which' command
        try {
            const whichResult = execSync('which google-chrome-stable 2>/dev/null || which chromium 2>/dev/null || which chromium-browser 2>/dev/null', { encoding: 'utf8' }).trim();
            if (whichResult) {
                possiblePaths.unshift(whichResult);
            }
        } catch (e) {
            // which command failed, continue with other methods
        }
    }
    
    // Check which path actually exists
    for (const path of possiblePaths) {
        if (path && fs.existsSync(path)) {
            console.log('✓ Found working Chrome/Chromium at:', path);
            return path;
        }
    }
    
    console.error('✗ No Chrome/Chromium executable found. Tried:', possiblePaths);
    throw new Error('Chrome/Chromium not found. Please ensure Chrome is installed.');
}

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: process.env.LARAVEL_URL || "http://127.0.0.1:6500",
        methods: ["GET", "POST"],
        credentials: true
    }
});

app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ limit: '50mb', extended: true }));

// Store active WhatsApp clients
const clients = new Map();

// Socket.IO connection
io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);

    socket.on('init-whatsapp', async (data) => {
        const { sessionId, userId, storeId } = data;
        
        console.log(`Initializing WhatsApp for session: ${sessionId}, store: ${storeId}`);

        // Check if client already exists
        if (clients.has(sessionId)) {
            console.log('Client already exists for session:', sessionId);
            const existingClient = clients.get(sessionId);
            
            // Check if still connected
            const state = await existingClient.getState();
            if (state === 'CONNECTED') {
                socket.emit('whatsapp-already-connected', { sessionId });
                
                // Get and send chats
                try {
                    const chats = await existingClient.getChats();
                    const chatList = await Promise.all(chats.slice(0, 50).map(async (chat) => {
                        const contact = await chat.getContact();
                        const lastMessage = chat.lastMessage;
                        
                        return {
                            id: chat.id._serialized,
                            name: chat.name || contact.pushname || contact.number,
                            isGroup: chat.isGroup,
                            unreadCount: chat.unreadCount,
                            timestamp: chat.timestamp,
                            lastMessage: lastMessage ? lastMessage.body : null
                        };
                    }));

                    socket.emit('chats-list', { sessionId, chats: chatList });
                } catch (error) {
                    console.error('Error getting chats:', error);
                }
                
                return;
            }
        }

        // Create new WhatsApp client
        const executablePath = findChromiumPath();
        console.log('Initializing WhatsApp client with Chromium at:', executablePath);
        
        const client = new Client({
            authStrategy: new LocalAuth({
                clientId: sessionId
            }),
            puppeteer: {
                headless: true,
                executablePath: executablePath,
                protocolTimeout: 90000, // 90 seconds
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--no-zygote',
                    '--disable-gpu',
                    '--disable-extensions',
                    '--disable-software-rasterizer',
                    '--disable-dev-tools',
                    '--no-default-browser-check',
                    '--disable-background-networking',
                    '--disable-sync',
                    '--metrics-recording-only',
                    '--mute-audio'
                ]
            },
            // Limit cache and data
            qrMaxRetries: 3,
            takeoverOnConflict: true
        });

        // QR Code event
        client.on('qr', async (qr) => {
            console.log('QR Code received for session:', sessionId);
            
            // Generate QR code as data URL
            const qrDataUrl = await qrcode.toDataURL(qr);
            
            // Send QR code to frontend
            socket.emit('qr-code', {
                sessionId,
                qrCode: qrDataUrl
            });
        });

        // Ready event
        client.on('ready', async () => {
            console.log('WhatsApp client is ready:', sessionId);
            
            const info = client.info;
            
            // Store client first
            clients.set(sessionId, client);
            
            const connectionData = {
                sessionId,
                userId,
                storeId,
                phone: info.wid.user,
                name: info.pushname,
                platform: info.platform
            };
            
            // Broadcast to ALL connected clients (not just the one who initiated)
            io.emit('whatsapp-connected', connectionData);
            
            // Also save directly to Laravel backend
            try {
                const laravelUrl = process.env.LARAVEL_URL || 'http://127.0.0.1:6500';
                const response = await fetch(`${laravelUrl}/webhook/whatsapp`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'qr_scanned',
                        session_id: sessionId,
                        user_id: userId,
                        store_id: storeId,
                        phone: info.wid.user,
                        name: info.pushname
                    })
                });
                console.log('Saved connection to Laravel:', response.status);
            } catch (error) {
                console.error('Failed to save connection to Laravel:', error.message);
            }
        });

        // Message event
        client.on('message', async (message) => {
            console.log('========================================');
            console.log('Message received:');
            console.log('  From:', message.from);
            console.log('  Type:', message.type);
            console.log('  Has Media:', message.hasMedia);
            console.log('  Body:', message.body || '(empty)');
            console.log('========================================');
            
            // Skip if message is from us
            if (message.fromMe) {
                return;
            }
            
            const chat = await message.getChat();
            const contact = await message.getContact();
            
            // Handle media messages (including voice/audio)
            let mediaUrl = null;
            let messageBody = message.body;
            
            if (message.hasMedia && (message.type === 'audio' || message.type === 'ptt' || message.type === 'voice')) {
                console.log('🎤 Voice/audio message detected, downloading media...');
                try {
                    const media = await message.downloadMedia();
                    if (media) {
                        console.log('  Media mimetype:', media.mimetype);
                        console.log('  Media data length:', media.data ? media.data.length : 0);
                        
                        // Convert media to base64 data URL for transcription
                        mediaUrl = `data:${media.mimetype};base64,${media.data}`;
                        console.log('✓ Audio media downloaded successfully, sending to Laravel for transcription...');
                    } else {
                        console.error('✗ downloadMedia returned null');
                    }
                } catch (error) {
                    console.error('✗ Error downloading audio media:', error.message);
                    console.error('Full error:', error);
                }
            }
            
            const messageData = {
                sessionId,
                userId,
                messageId: message.id._serialized,
                from: message.from,
                to: message.to,
                body: messageBody,
                type: message.type,
                timestamp: message.timestamp,
                isGroup: chat.isGroup,
                contactName: contact.pushname || contact.name || message.from,
                sender: message.fromMe ? 'outgoing' : 'incoming',
                hasMedia: message.hasMedia
            };
            
            // Broadcast message to all connected clients
            io.emit('new-message', messageData);
            console.log('Broadcasted message to all clients');
            
            // Send to Laravel for AI processing (only for incoming messages)
            if (!message.fromMe) {
                console.log('Attempting to send message to Laravel for AI processing...');
                try {
                    const laravelUrl = process.env.LARAVEL_URL || 'http://127.0.0.1:6500';
                    const processUrl = `${laravelUrl}/api/whatsapp/process-message`;
                    console.log('Sending request to:', processUrl);
                    
                    const requestBody = {
                        session_id: sessionId,
                        user_id: userId,
                        from: message.from,
                        message: messageBody,
                        message_id: message.id._serialized,
                        contact_name: contact.pushname || contact.name || message.from,
                        type: message.type,
                        media_url: mediaUrl
                    };
                    
                    console.log('Request includes media_url:', mediaUrl ? 'YES (' + mediaUrl.length + ' chars)' : 'NO');
                    
                    const response = await fetch(processUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestBody)
                    });
                    
                    console.log('Laravel API response status:', response.status);
                    
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Laravel API response:', {
                            success: result.success,
                            ai_response: result.ai_response ? result.ai_response.substring(0, 100) + '...' : null,
                            media_type: result.media_type,
                            has_media_url: !!result.media_url
                        });
                        
                        // If AI generated a response, send it
                        if (result.ai_response) {
                            console.log('Sending AI response...');
                            
                            // Check if there's a media URL (image) to send
                            if (result.media_url && result.media_type === 'image') {
                                console.log('📷 Sending image with caption:', result.media_url);
                                try {
                                    // Fetch the image from the URL
                                    const imageResponse = await fetch(result.media_url);
                                    if (!imageResponse.ok) {
                                        throw new Error('Failed to fetch image: ' + imageResponse.status);
                                    }
                                    const arrayBuffer = await imageResponse.arrayBuffer();
                                    const buffer = Buffer.from(arrayBuffer);
                                    const base64 = buffer.toString('base64');
                                    
                                    // Determine mimetype from URL or default to jpeg
                                    let mimetype = 'image/jpeg';
                                    const lowerUrl = result.media_url.toLowerCase();
                                    if (lowerUrl.includes('.png')) {
                                        mimetype = 'image/png';
                                    } else if (lowerUrl.includes('.gif')) {
                                        mimetype = 'image/gif';
                                    } else if (lowerUrl.includes('.webp')) {
                                        mimetype = 'image/webp';
                                    }
                                    
                                    const media = new MessageMedia(mimetype, base64);
                                    await client.sendMessage(message.from, media, { caption: result.ai_response });
                                    console.log('✓ Image sent successfully with caption');
                                } catch (imageError) {
                                    console.error('✗ Error sending image, sending text only:', imageError.message);
                                    // Fallback to text-only if image fails
                                    await client.sendMessage(message.from, result.ai_response);
                                }
                            } else {
                                // Send text-only response
                                await client.sendMessage(message.from, result.ai_response);
                                console.log('✓ Text response sent successfully');
                            }
                        } else {
                            console.log('No AI response generated');
                        }
                    } else {
                        console.error('Laravel API returned error status:', response.status);
                        const errorText = await response.text();
                        console.error('Error response:', errorText);
                    }
                } catch (error) {
                    console.error('Error processing message with AI:', error.message);
                    console.error('Full error:', error);
                }
            }
        });

        // Disconnected event
        client.on('disconnected', (reason) => {
            console.log('WhatsApp disconnected:', sessionId, reason);
            socket.emit('whatsapp-disconnected', { sessionId, reason });
            clients.delete(sessionId);
        });

        // Auth failure event
        client.on('auth_failure', (msg) => {
            console.log('Authentication failure:', sessionId, msg);
            socket.emit('whatsapp-error', { sessionId, error: 'Authentication failed' });
            clients.delete(sessionId);
        });

        // Error handling
        client.on('error', (error) => {
            console.error('WhatsApp client error:', sessionId, error);
        });

        // Initialize client with error handling
        try {
            await client.initialize();
        } catch (error) {
            console.error('Failed to initialize WhatsApp client:', error);
            socket.emit('whatsapp-error', { sessionId, error: 'Failed to initialize' });
        }
    });

    socket.on('send-message', async (data) => {
        const { sessionId, to, message } = data;
        const client = clients.get(sessionId);

        if (!client) {
            console.error('Client not found for session:', sessionId);
            socket.emit('error', { message: 'Client not found' });
            return;
        }

        try {
            // Check if client is ready
            const state = await client.getState();
            console.log('Client state before sending:', state);
            
            if (state !== 'CONNECTED') {
                console.error('Client not connected, state:', state);
                socket.emit('error', { message: 'WhatsApp is not connected. Please reconnect.' });
                return;
            }

            // Don't modify the chat ID if it already has @ symbol (like @lid or @c.us)
            const chatId = to.includes('@') ? to : `${to}@c.us`;
            console.log('Sending message to:', chatId, 'Message:', message);
            
            const sentMessage = await client.sendMessage(chatId, message);
            console.log('Message sent successfully:', sentMessage.id._serialized);
            
            socket.emit('message-sent', {
                messageId: sentMessage.id._serialized,
                timestamp: sentMessage.timestamp
            });
        } catch (error) {
            console.error('Error sending message:', error.message || error);
            socket.emit('error', { message: 'Failed to send message: ' + (error.message || 'Unknown error') });
        }
    });

    socket.on('reconnect-session', async (data) => {
        const { sessionId } = data;
        
        console.log(`Attempting to reconnect session: ${sessionId}`);

        // Check if client exists in memory
        if (clients.has(sessionId)) {
            const client = clients.get(sessionId);
            const state = await client.getState();
            
            if (state === 'CONNECTED') {
                socket.emit('whatsapp-already-connected', { sessionId });
                return;
            }
        }

        // Try to initialize from saved session
        socket.emit('init-whatsapp', data);
    });

    socket.on('get-chats', async (data) => {
        const { sessionId } = data;
        const client = clients.get(sessionId);

        if (!client) {
            socket.emit('error', { message: 'Client not found' });
            return;
        }

        try {
            const chats = await client.getChats();
            // Limit to 50 most recent chats to save memory
            const limitedChats = chats.slice(0, 50);
            
            const chatList = await Promise.all(limitedChats.map(async (chat) => {
                try {
                    const contact = await chat.getContact();
                    const lastMessage = chat.lastMessage;
                    
                    return {
                        id: chat.id._serialized,
                        name: chat.name || contact.pushname || contact.number,
                        isGroup: chat.isGroup,
                        unreadCount: chat.unreadCount,
                        timestamp: chat.timestamp,
                        lastMessage: lastMessage ? lastMessage.body : null
                    };
                } catch (err) {
                    console.error('Error processing chat:', err.message);
                    // Return basic info if contact fetch fails
                    return {
                        id: chat.id._serialized,
                        name: chat.name || 'Unknown',
                        isGroup: chat.isGroup,
                        unreadCount: chat.unreadCount,
                        timestamp: chat.timestamp,
                        lastMessage: null
                    };
                }
            }));

            socket.emit('chats-list', { sessionId, chats: chatList });
        } catch (error) {
            console.error('Error getting chats:', error);
            socket.emit('error', { message: 'Failed to get chats' });
        }
    });

    socket.on('get-messages', async (data) => {
        const { sessionId, chatId, limit = 50 } = data;
        const client = clients.get(sessionId);

        if (!client) {
            socket.emit('error', { message: 'Client not found' });
            return;
        }

        try {
            const chat = await client.getChatById(chatId);
            const messages = await chat.fetchMessages({ limit });

            const messageList = messages.map(msg => ({
                id: msg.id._serialized,
                body: msg.body,
                type: msg.type,
                timestamp: msg.timestamp,
                from: msg.from,
                to: msg.to,
                sender: msg.fromMe ? 'outgoing' : 'incoming',
                hasMedia: msg.hasMedia
            }));

            socket.emit('messages-list', { sessionId, chatId, messages: messageList });
        } catch (error) {
            console.error('Error getting messages:', error);
            socket.emit('error', { message: 'Failed to get messages' });
        }
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id);
    });
});

// REST API endpoints
app.post('/api/disconnect', (req, res) => {
    const { sessionId } = req.body;
    const client = clients.get(sessionId);

    if (client) {
        client.destroy();
        clients.delete(sessionId);
        res.json({ success: true, message: 'Client disconnected' });
    } else {
        res.status(404).json({ success: false, message: 'Client not found' });
    }
});

app.get('/api/status', (req, res) => {
    res.json({
        success: true,
        activeSessions: clients.size
    });
});

const PORT = process.env.PORT || process.env.WHATSAPP_PORT || 3000;

server.listen(PORT, '0.0.0.0', () => {
    console.log(`WhatsApp service running on port ${PORT}`);
    console.log(`Laravel URL: ${process.env.LARAVEL_URL || 'http://127.0.0.1:6500'}`);
});
