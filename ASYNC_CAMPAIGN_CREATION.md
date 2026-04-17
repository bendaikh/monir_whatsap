# Asynchronous Campaign Creation

## Overview

Campaign creation now works asynchronously, just like landing page generation. When you create a campaign, it's saved immediately to the database with a "Generating" status, and the actual API calls to Facebook/TikTok happen in the background via a queue job.

## How It Works

### 1. Create a Campaign
- Fill out the campaign form with all details
- Upload media files
- Click "Create Campaign"
- **The campaign is created immediately** and you're redirected to the campaigns page

### 2. Campaign Statuses

The system uses 4 statuses:

- **Pending** (Generating): Campaign record created, waiting for processing
- **Processing** (Creating): Currently making API calls to Facebook/TikTok
- **Completed**: Successfully created on the platform(s)
- **Failed**: An error occurred during creation (error message is displayed)

### 3. Real-Time Updates

- The campaigns page **auto-refreshes every 10 seconds** when there are generating campaigns
- You can see the status change in real-time
- A spinning icon indicates campaigns being generated
- You can **create multiple campaigns** while others are being generated

### 4. Database Structure

A new `campaigns` table stores:
- Campaign name, objective, budget
- Selected platforms (Facebook, TikTok, or both)
- Status (pending, processing, completed, failed)
- All campaign data (form inputs, media file paths)
- Facebook/TikTok campaign IDs once created
- Error messages if failed

### 5. Background Job

The `CreateCampaignJob` handles:
1. Updating status to "processing"
2. Uploading media files to platforms
3. Creating campaigns via API calls
4. Creating ad sets and ads
5. Updating status to "completed" or "failed"
6. Cleaning up temporary media files

## Benefits

1. **No More Waiting**: Create campaigns instantly without waiting for API calls
2. **Parallel Creation**: Create multiple campaigns while others are being generated
3. **Better UX**: See progress in real-time with status updates
4. **Error Handling**: Failed campaigns are clearly marked with error messages
5. **Reliable**: Uses Laravel's queue system for reliable background processing

## Queue Setup

Make sure your queue is running:

```bash
# Development
php artisan queue:work

# Production (supervisor recommended)
php artisan queue:work --queue=default --tries=3
```

## Testing

1. Create a campaign with Facebook or TikTok
2. You'll be immediately redirected to the campaigns page
3. See your campaign with "Generating" status
4. The page auto-refreshes every 10 seconds
5. Status changes to "Creating" then "Completed"
6. Campaign details are displayed once completed

## Error Handling

If a campaign fails:
- Status shows as "Failed" with a red badge
- Error message is displayed on hover
- You can view the full error in the logs
- The campaign record is preserved for debugging

## Comparison with Old Behavior

### Before (Synchronous)
1. Fill form and submit
2. ⏳ Wait 20-60 seconds while API calls are made
3. Can't do anything else during this time
4. Finally redirected to campaigns page

### After (Asynchronous)
1. Fill form and submit
2. ✅ Immediately redirected with success message
3. 🚀 Create more campaigns while first one generates
4. 👀 Watch status updates in real-time
5. 🔄 Page auto-refreshes until all campaigns are ready

## Files Created/Modified

### New Files
- `database/migrations/2026_04_17_000001_create_campaigns_table.php` - Database table
- `app/Models/Campaign.php` - Campaign model
- `app/Jobs/CreateCampaignJob.php` - Background job for campaign creation

### Modified Files
- `app/Http/Controllers/CampaignCreatorController.php` - Now creates record and dispatches job
- `app/Http/Controllers/AdCampaignsController.php` - Shows local campaigns from database
- `resources/views/customer/ad-campaigns.blade.php` - Shows status badges and auto-refresh

## Notes

- Media files are stored temporarily and cleaned up after upload
- The queue must be running for campaigns to be processed
- If the queue is not running, campaigns will stay in "Pending" status
- You can manually process pending campaigns by running: `php artisan queue:work`
