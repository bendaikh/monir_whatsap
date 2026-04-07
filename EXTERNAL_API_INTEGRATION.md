# External API Integration - System Connect

## Overview
The System Connect feature allows you to automatically push orders from your landing page form submissions to an external application via API.

## Setup Instructions

### 1. Configure Your External API
Based on the screenshot provided, your external API should have:
- Base URL: `https://chateasy.site/api` (or your custom URL)
- Authentication: Bearer token in Authorization header
- Endpoint: `POST /api/orders` to create new orders

### 2. Configure in ChatEasy Dashboard

1. Navigate to **System Connect** in the sidebar
2. Enter your **Base API URL** (without the `/api/orders` endpoint)
   - Example: `https://chateasy.site/api`
3. Enter your **API Key** (the authentication token)
4. Check **Enable API Integration** to activate
5. Click **Save Settings**
6. Click **Test API Connection** to verify it works

### 3. How It Works

When a customer fills out the form on your product landing page:
1. A lead is created in your ChatEasy database
2. A background job (`PushOrderToExternalApi`) is dispatched
3. The job sends the order data to your external API
4. The order is created in your external application

### Order Data Structure

The following data is sent to your external API:

```json
{
  "customer_name": "John Doe",
  "customer_phone": "+212600000000",
  "product_id": 1,
  "product_name": "Product Name",
  "product_price": 299.99,
  "note": "Customer's optional note",
  "language": "fr",
  "source": "landing_page",
  "lead_id": 123,
  "created_at": "2026-04-07T12:34:56Z"
}
```

### Expected API Response

Your external API should return a successful response (status 200-299) when the order is created successfully.

## Technical Details

### Files Created/Modified

1. **Migration**: `2026_04_07_000839_add_external_api_settings_to_users_table.php`
   - Adds `external_api_url`, `external_api_key_encrypted`, and `external_api_enabled` to users table

2. **Service**: `app/Services/ExternalApiService.php`
   - Handles API communication
   - Encrypts/decrypts API keys
   - Tests connections

3. **Job**: `app/Jobs/PushOrderToExternalApi.php`
   - Runs in background queue
   - Automatically pushes leads to external API
   - Logs success/failures

4. **Controller Methods**: `CustomerDashboardController.php`
   - `externalApiSettings()` - Shows settings page
   - `saveExternalApiSettings()` - Saves API configuration
   - `testExternalApiConnection()` - Tests the API

5. **View**: `resources/views/customer/external-api-settings.blade.php`
   - User interface for configuration

6. **Routes**: Added to `routes/web.php`
   - GET `/app/external-api-settings`
   - POST `/app/external-api-settings`
   - POST `/app/external-api-settings/test`

### Security

- API keys are encrypted using Laravel's encryption (`Crypt::encryptString`)
- Keys are never displayed in plain text after being saved
- All API calls are logged for debugging

### Queue System

The integration uses Laravel's queue system:
- Jobs are processed by `php artisan queue:work`
- Failed jobs can be retried automatically
- Logs are written to `storage/logs/laravel.log`

## Troubleshooting

### Connection Test Fails

1. Verify your API URL is correct (without trailing slash)
2. Check that your API key is valid
3. Ensure your external API is accessible from the server
4. Check your external API expects a Bearer token authentication

### Orders Not Appearing in External App

1. Check `storage/logs/laravel.log` for errors
2. Verify the queue worker is running: `php artisan queue:work`
3. Test the API connection in settings
4. Check if the integration is enabled

### View Logs

To see detailed logs about API calls:
```bash
tail -f storage/logs/laravel.log | grep "external API"
```

## API Endpoint Requirements

Your external API should support:

### POST /api/orders
- **Method**: POST
- **Headers**:
  - `Authorization: Bearer {your_api_key}`
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body**: JSON object with order data (see structure above)
- **Response**: 200-299 status code for success

### GET /api/orders (used for testing)
- **Method**: GET
- **Headers**:
  - `Authorization: Bearer {your_api_key}`
  - `Accept: application/json`
- **Response**: List of orders or empty array

## Support

If you need help:
1. Check the logs in `storage/logs/laravel.log`
2. Verify your external API documentation
3. Test the API manually using a tool like Postman
4. Contact your API provider if authentication fails
