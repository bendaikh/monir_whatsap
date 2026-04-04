# Production Queue Setup for Landing Page & Image Generation

## Overview
Your application uses Laravel queued jobs for:
- **Landing Page Generation** (`GenerateProductLandingPageJob`)
- **AI Image Generation** (`GenerateProductImagesJob`)

These jobs run asynchronously in the background to avoid blocking the user interface.

## Production Setup Required

### 1. Configure Queue Driver

In your production `.env` file on Railway, set:

```env
QUEUE_CONNECTION=database
```

This uses the database to store queued jobs (recommended for Railway as it's already included).

### 2. Run the Queue Table Migration

Make sure this migration has run (it should auto-run on Railway):

```bash
php artisan queue:table
php artisan migrate
```

This creates the `jobs`, `failed_jobs`, and `job_batches` tables.

### 3. Set Up the Queue Worker on Railway

Railway needs to run the queue worker continuously. Here's how:

#### Option A: Using Procfile (Recommended for Railway)

Create a file called `Procfile` in your project root:

```
web: php artisan serve --host=0.0.0.0 --port=$PORT
worker: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

This tells Railway to run:
- **web**: Your Laravel application
- **worker**: The queue worker process

#### Option B: Railway Service Configuration

In Railway dashboard:
1. Go to your project
2. Click on your service
3. Go to **Settings** → **Start Command**
4. Add this command:
```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --daemon
```

**However**, you'll need TWO Railway services:
- Service 1: Web application (Laravel)
- Service 2: Queue worker (same code, different start command)

### 4. Laravel Scheduler (For Automatic Job Cleanup)

Add to `app/Console/Kernel.php` in the `schedule()` method:

```php
protected function schedule(Schedule $schedule)
{
    // Retry failed jobs automatically
    $schedule->command('queue:retry all')->hourly();
    
    // Clean up old failed jobs (older than 7 days)
    $schedule->command('queue:flush --hours=168')->daily();
    
    // Prune old jobs from the database
    $schedule->command('queue:prune-batches --hours=48')->daily();
    $schedule->command('queue:prune-failed --hours=168')->daily();
}
```

Then set up a cron job on Railway to run the Laravel scheduler every minute.

### 5. Railway Cron Job Setup

In Railway dashboard, add a cron service or use the scheduler:

1. Create a new service called "Scheduler"
2. Use the same repository
3. Set the start command to:
```bash
while true; do php artisan schedule:run; sleep 60; done
```

**OR** use Railway's built-in cron (if available):
```bash
* * * * * cd /app && php artisan schedule:run >> /dev/null 2>&1
```

## Monitoring Queue Jobs

### Check Queue Status

```bash
# See pending jobs
php artisan queue:monitor

# See failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry {job-id}
```

### Enable Horizon (Optional - Better Dashboard)

For a better queue monitoring UI, install Laravel Horizon:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

Then run:
```bash
php artisan horizon
```

This gives you a web dashboard at `/horizon` to monitor queues.

## Recommended Railway Setup

### Service 1: Web Application
**Name**: `chateasy-web`
**Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
**Environment Variables**:
```
QUEUE_CONNECTION=database
```

### Service 2: Queue Worker
**Name**: `chateasy-queue-worker`
**Start Command**: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
**Environment Variables**: (Same as web app)
```
QUEUE_CONNECTION=database
DB_CONNECTION=mysql (or your DB type)
DB_HOST=xxx
DB_PORT=xxx
DB_DATABASE=xxx
DB_USERNAME=xxx
DB_PASSWORD=xxx
```

### Service 3: Scheduler (Optional but Recommended)
**Name**: `chateasy-scheduler`
**Start Command**: 
```bash
while true; do php artisan schedule:run; sleep 60; done
```

## Testing Queue Jobs

### Test Locally

```bash
# Run queue worker locally
php artisan queue:work

# Dispatch a test job (in tinker)
php artisan tinker
>>> $product = App\Models\Product::first();
>>> App\Jobs\GenerateProductLandingPageJob::dispatch($product, 1);
```

### Test on Railway

1. Check Railway logs for "worker" service
2. Create a product with landing page generation
3. Watch the logs to see job processing
4. Check product status updates

## Performance Tuning

### Queue Worker Options

```bash
# Basic (default)
php artisan queue:work

# Production recommended
php artisan queue:work \
  --sleep=3 \           # Sleep 3 seconds when no jobs
  --tries=3 \           # Retry failed jobs 3 times
  --max-time=3600 \     # Restart worker after 1 hour
  --daemon              # Run as daemon
```

### Multiple Workers (for heavy load)

```bash
# Run 3 workers in parallel
php artisan queue:work --queue=default --sleep=3 &
php artisan queue:work --queue=default --sleep=3 &
php artisan queue:work --queue=default --sleep=3 &
```

## Cost Optimization for Railway

Since Railway charges based on usage:

### Option 1: Single Service with Supervisor

Use **Supervisor** to run both web and worker in one service:

Install Supervisor, then create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/app/storage/logs/worker.log
```

Then modify your Railway start command:
```bash
supervisord -c /etc/supervisor/supervisord.conf && php artisan serve --host=0.0.0.0 --port=$PORT
```

### Option 2: Use Railway's Free Tier Wisely

- Web service: Always running
- Worker service: Only when needed (can be stopped when not generating)
- Scheduler: Very lightweight, minimal cost

## Troubleshooting

### Jobs Not Processing?

1. Check if queue worker is running:
   ```bash
   ps aux | grep queue:work
   ```

2. Check Railway logs for the worker service

3. Verify database connection:
   ```bash
   php artisan queue:monitor
   ```

4. Check failed jobs:
   ```bash
   php artisan queue:failed
   ```

### Job Fails Repeatedly?

1. Check logs in `storage/logs/laravel.log`
2. Increase timeout in job: `public $timeout = 300;`
3. Increase memory: `public $memory = 512;`
4. Add retry delay: `public $retryAfter = 60;`

## Summary

**Minimum Setup for Production:**

1. ✅ Set `QUEUE_CONNECTION=database` in Railway env vars
2. ✅ Add a separate "Queue Worker" service on Railway
3. ✅ Set worker start command: `php artisan queue:work --sleep=3 --tries=3`
4. ✅ (Optional) Add scheduler service for job cleanup

**Recommended Setup:**
- Use Horizon for better monitoring
- Set up multiple workers for heavy loads
- Use supervisor for single-service deployment
- Monitor Railway logs regularly

Your jobs will now process automatically in the background! 🚀
