# WhatsApp AI Features Documentation

## Overview

The WhatsApp integration now supports two major features:
1. **Voice Message Transcription** - AI can understand and respond to voice recordings
2. **Product Image Sending** - AI can send product images to customers

---

## 1. Voice Message Transcription

### How It Works

When a customer sends a voice recording (audio message):

1. **Node.js Backend** (`whatsapp-service/server.js`):
   - Detects voice/audio messages (`type: 'audio'` or `type: 'ptt'`)
   - Downloads the audio media from WhatsApp
   - Converts it to base64 data URL
   - Sends the audio data to Laravel API

2. **Laravel Backend** (`app/Http/Controllers/WhatsAppController.php`):
   - Receives the voice message with `media_url` parameter
   - Downloads the audio file from the data URL
   - Calls OpenAI Whisper API to transcribe the audio to text
   - Processes the transcribed text with AI for generating response
   - Returns AI response to Node.js

3. **Node.js sends the response** back to the customer

### Requirements

- OpenAI API key (configured in AI Settings)
- The same API key used for chat is used for Whisper transcription

### Supported Audio Formats

- MP3
- MP4/M4A
- MPEG/MPGA
- WAV
- WebM
- OGG (WhatsApp default format)

### Code Flow

```
Customer sends voice → 
WhatsApp Web → 
Node.js downloads media → 
Laravel receives audio URL → 
Whisper transcribes → 
AI processes text → 
Response sent back
```

---

## 2. Product Image Sending

### How It Works

When a customer asks about products:

1. **AI Recognition**:
   - AI analyzes the customer's message
   - Determines if sending a product image would be helpful
   - Looks up product information from the database

2. **Image Tagging**:
   - AI includes a special tag in its response: `[SEND_IMAGE:product_id]`
   - Example: "Here's our amazing Blue T-Shirt! [SEND_IMAGE:5] It's available in sizes S, M, L for $29.99"

3. **Image Processing** (`app/Services/AiChatService.php`):
   - Laravel extracts the product ID from the tag
   - Looks up the product's main image
   - Removes the tag from the text
   - Returns both text and image URL to Node.js

4. **Image Sending** (`whatsapp-service/server.js`):
   - Node.js receives `ai_response`, `media_url`, and `media_type`
   - Downloads the image from the URL
   - Converts to base64
   - Sends via WhatsApp with the text as caption

### AI Instructions

The AI is instructed to:
- Only suggest images for products that exist in the product list
- Send ONE image per response (not multiple)
- Use images when customers show interest or ask about specific products
- Include the product ID from the available products list

### Example Scenarios

**Scenario 1: Customer asks about a product**
```
Customer: "Do you have blue t-shirts?"
AI Response: "Yes! We have a beautiful Blue Cotton T-Shirt! [SEND_IMAGE:5] Available in S, M, L, XL for $29.99 😊"
Result: Customer receives image with caption
```

**Scenario 2: Customer browses products**
```
Customer: "What's your best seller?"
AI Response: "Our best seller is the Premium Leather Wallet! [SEND_IMAGE:12] It's made from genuine leather, has 8 card slots, and costs only $49.99"
Result: Customer receives wallet image with description
```

**Scenario 3: No relevant product**
```
Customer: "What's your return policy?"
AI Response: "Our return policy allows returns within 30 days of purchase..."
Result: Text-only response (no image needed)
```

---

## Database Schema

### Messages Table

The `messages` table supports both features:

```sql
type: ENUM('text', 'image', 'video', 'audio', 'document', 'location')
direction: ENUM('incoming', 'outgoing')
content: TEXT (message text or transcription)
media_url: VARCHAR (URL to media file)
is_ai_response: BOOLEAN
is_read: BOOLEAN
whatsapp_message_id: VARCHAR
```

---

## API Response Format

### Laravel to Node.js Response

```json
{
  "success": true,
  "ai_response": "Here's our product! It's amazing and costs $29.99",
  "media_type": "image",
  "media_url": "https://yoursite.com/storage/products/image.jpg"
}
```

**Fields:**
- `success` (boolean): Whether the processing succeeded
- `ai_response` (string|null): The text response to send
- `media_type` (string|null): Type of media ('image' or null)
- `media_url` (string|null): Full URL to the media file

---

## Configuration

### Environment Variables

No additional environment variables needed. Uses existing:
- `LARAVEL_URL` - Laravel backend URL (default: http://127.0.0.1:6500)
- OpenAI API key (configured in AI Settings page)

### AI Settings Page

Make sure to:
1. Enable "Auto-Reply" toggle
2. Configure OpenAI API key (required for both chat and voice transcription)
3. Select OpenAI model (default: gpt-3.5-turbo)

---

## Testing

### Test Voice Messages

1. Send a voice recording from WhatsApp
2. Check Node.js console for: "Voice/audio message detected, downloading media..."
3. Check Laravel logs for: "Processing audio message"
4. Verify transcription appears in logs: "Audio transcribed successfully"
5. Customer should receive AI response based on transcribed text

### Test Image Sending

1. Send message asking about a product (e.g., "Show me your t-shirts")
2. AI should respond with product information
3. If product has an image, customer receives:
   - Product image
   - Text description as caption
4. Check Node.js console for: "Sending image with caption"

---

## Troubleshooting

### Voice Messages Not Working

**Issue**: Voice messages are ignored or not transcribed

**Solutions**:
- Verify OpenAI API key is configured
- Check Laravel logs for transcription errors
- Ensure `storage/logs/laravel.log` shows: "Processing audio message"
- Verify audio file is being downloaded (check temp directory)

### Images Not Sending

**Issue**: AI responds but no image is sent

**Solutions**:
- Check if product has `main_image` set in database
- Verify image file exists in `storage/app/public/products/`
- Check Laravel logs for: "Product image extracted"
- Ensure `storage` symlink is created: `php artisan storage:link`
- Verify image URL is accessible from Node.js server

### AI Not Suggesting Images

**Issue**: AI responds with text only, never suggests images

**Solutions**:
- Verify products exist and are active in database
- Check that products have images uploaded
- Test by explicitly asking: "Show me product [name]"
- Review AI system prompt in `AiChatService.php`

---

## File Changes Summary

### Modified Files

1. **app/Http/Controllers/WhatsAppController.php**
   - Added `transcribeAudio()` method for Whisper API
   - Added `getAudioExtension()` helper method
   - Updated `processMessageWithAi()` to handle audio and media responses

2. **app/Services/AiChatService.php**
   - Added `generateResponseWithMedia()` method
   - Added `extractProductImage()` method
   - Updated `buildSystemPrompt()` with image sending instructions
   - Updated `getProductsContext()` to include product IDs and image info
   - Modified OpenAI and Anthropic response methods to support media

3. **whatsapp-service/server.js**
   - Added MessageMedia import
   - Updated message handler to download audio media
   - Added image sending capability with base64 conversion
   - Enhanced logging for debugging

---

## Future Enhancements

Potential improvements:
- Support for sending multiple product images in a carousel
- Video message support
- Document handling and OCR
- Product image generation via AI
- Voice response synthesis (TTS)
- Multi-language transcription

---

## Support

For issues or questions, check:
- Laravel logs: `storage/logs/laravel.log`
- Node.js console output
- WhatsApp Web DevTools console

---

Last Updated: 2026-04-19
