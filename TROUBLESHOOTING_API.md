# TROUBLESHOOTING: External API Integration

## Issue Found
Your orders are being sent to the external API, but they're failing with a **401 Unauthenticated** error.

## Root Cause
The API authentication is not working properly. This could be because:
1. The API key is incorrect
2. The API key format is wrong
3. The external API expects a different authentication method

## Solutions to Try

### Solution 1: Update the Base URL (IMPORTANT!)
Your current URL is set to: `https://smanager.site/api`

This needs to be changed to: `https://smanager.site` (without /api)

**Steps:**
1. Go to **System Connect** in your dashboard
2. Change the Base API URL from `https://smanager.site/api` to `https://smanager.site`
3. Click **Save Settings**
4. Click **Test API Connection**

### Solution 2: Verify Your API Key
Based on your screenshot, your API key should look like:
`lapi_e4P9103N1Y5dt1s>5421P7hb3nc9LcH17PNb1Y0L...`

**Steps:**
1. Go to your external application (smanager.site)
2. Navigate to the Custom API Integration settings
3. **Regenerate** or copy the complete API key
4. Go to **System Connect** in ChatEasy dashboard
5. Paste the COMPLETE API key (including `lapi_` prefix)
6. Make sure to check "Enable API Integration"
7. Click **Save Settings**
8. Click **Test API Connection**

### Solution 3: Check API Authentication Header Format

The external API might expect a different header format. Currently we're sending:
```
Authorization: Bearer {your_api_key}
```

If your API expects a different format (like `X-API-Key` or just the key without "Bearer"), we need to modify the code.

## Testing Your Setup

### Test 1: Manual API Call
You can test your API manually using PowerShell:

```powershell
$headers = @{
    "Authorization" = "Bearer YOUR_API_KEY_HERE"
    "Accept" = "application/json"
}
Invoke-RestMethod -Uri "https://smanager.site/api/orders" -Method GET -Headers $headers
```

Replace `YOUR_API_KEY_HERE` with your actual API key.

### Test 2: Using the Dashboard
1. Go to **System Connect**
2. Ensure settings are correct:
   - Base URL: `https://smanager.site` (NO /api at the end)
   - API Key: Your complete API key from the external app
   - Integration enabled: ✓ Checked
3. Click **Test API Connection**
4. If successful, submit a test order from your landing page

### Test 3: Check Logs
After submitting an order, check the logs:

```powershell
cd C:\Users\Espacegamers\Documents\chateasy
Get-Content storage\logs\laravel.log -Tail 50
```

Look for lines containing "external API" to see detailed error messages.

## Current Status

✅ **Working:**
- Lead creation from landing page
- Job dispatching to queue
- Queue worker processing jobs
- URL construction (now fixed)
- Data formatting

❌ **Not Working:**
- API Authentication (401 error)

## Next Steps

1. **Update the Base URL** to remove `/api` from the end
2. **Verify your API key** is correct and complete
3. **Test the connection** using the Test button
4. **Submit a new test order** from your landing page
5. **Check the logs** to see if the order was successfully pushed

## If Still Not Working

If after following all steps above it still doesn't work, you may need to:

1. Check with your external API provider about the correct authentication format
2. Verify the API endpoint is actually `POST /api/orders`
3. Check if there are any CORS or firewall restrictions
4. Ensure the external API is accessible from your server

## Quick Fix Script

Run this to check your current configuration:

```powershell
cd C:\Users\Espacegamers\Documents\chateasy
php test-api.php
```

This will show you:
- Current API URL configuration
- Actual endpoint being called
- Test connection result
- Attempt to create an order
- Detailed error messages

## Need Help?

Contact your external API provider (smanager.site) and ask:
1. What is the correct Base URL for API integration?
2. How should the API key be sent in the request header?
3. What is the exact endpoint for creating orders?
4. Are there any IP whitelist or security restrictions?
