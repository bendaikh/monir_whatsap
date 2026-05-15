<?php
/**
 * Server Configuration Checker
 * 
 * This script checks your web server configuration and helps you with Step 3
 * 
 * Usage:
 * 1. Upload this file to your server's public directory
 * 2. Visit: https://redsharkpro.com/check-server.php
 * 3. Follow the instructions shown
 */

$domain = $_GET['domain'] ?? 'tanzaniaship.com';
$serverIP = '178.16.128.28';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Configuration Checker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .info-box {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .success-box {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .warning-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .step {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .step h3 {
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .code-box {
            background: #1e293b;
            color: #10b981;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            margin: 10px 0;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            background: white;
        }
        .check-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .check-icon.ok { background: #22c55e; color: white; }
        .check-icon.warning { background: #f59e0b; color: white; }
        .check-icon.error { background: #ef4444; color: white; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover { background: #2563eb; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f1f5f9;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Server Configuration Checker</h1>
            <p>Check your server setup for custom domain: <strong><?= htmlspecialchars($domain) ?></strong></p>
        </div>
        
        <div class="content">
            
            <!-- System Information -->
            <div class="success-box">
                <h2 style="margin-bottom: 15px;">✅ System Information</h2>
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Current Domain</td>
                        <td><strong><?= $_SERVER['HTTP_HOST'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>Server IP</td>
                        <td><strong><?= $_SERVER['SERVER_ADDR'] ?? 'N/A' ?></strong></td>
                    </tr>
                    <tr>
                        <td>Web Server</td>
                        <td><strong><?= $_SERVER['SERVER_SOFTWARE'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>PHP Version</td>
                        <td><strong><?= PHP_VERSION ?></strong></td>
                    </tr>
                    <tr>
                        <td>Document Root</td>
                        <td><strong><?= $_SERVER['DOCUMENT_ROOT'] ?></strong></td>
                    </tr>
                </table>
            </div>

            <!-- DNS Check -->
            <div class="info-box">
                <h2 style="margin-bottom: 15px;">🌐 DNS Configuration Check</h2>
                <?php
                $dnsCheck = @dns_get_record($domain, DNS_A);
                if ($dnsCheck && count($dnsCheck) > 0):
                    $resolvedIP = $dnsCheck[0]['ip'] ?? 'Unknown';
                    $isCorrect = $resolvedIP === $serverIP;
                ?>
                    <div class="check-item">
                        <div class="check-icon <?= $isCorrect ? 'ok' : 'warning' ?>">
                            <?= $isCorrect ? '✓' : '!' ?>
                        </div>
                        <div>
                            <strong><?= htmlspecialchars($domain) ?></strong> resolves to 
                            <strong><?= htmlspecialchars($resolvedIP) ?></strong>
                            <?php if ($isCorrect): ?>
                                <span style="color: #22c55e;">✓ Correct!</span>
                            <?php else: ?>
                                <span style="color: #f59e0b;">⚠ Should be <?= $serverIP ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="check-item">
                        <div class="check-icon error">✗</div>
                        <div>
                            <strong><?= htmlspecialchars($domain) ?></strong> is not resolving yet.
                            <br><small>Wait for DNS propagation (1-24 hours) or check your DNS records.</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Step 3: Web Server Configuration -->
            <div class="step">
                <h3>📋 Step 3: Configure Your Web Server</h3>
                
                <p style="margin-bottom: 15px;">Based on your server software: <strong><?= $_SERVER['SERVER_SOFTWARE'] ?></strong></p>
                
                <?php
                $serverSoftware = strtolower($_SERVER['SERVER_SOFTWARE']);
                $isApache = strpos($serverSoftware, 'apache') !== false;
                $isNginx = strpos($serverSoftware, 'nginx') !== false;
                ?>
                
                <?php if ($isApache): ?>
                    <div class="warning-box">
                        <h4 style="margin-bottom: 10px;">🔧 Apache Configuration</h4>
                        <p>You need to add your domain to the VirtualHost configuration.</p>
                        
                        <h5 style="margin-top: 15px; margin-bottom: 10px;">Option 1: Contact Your Hosting Provider</h5>
                        <p>Tell them: "Please add <strong><?= htmlspecialchars($domain) ?></strong> as an alias/addon domain to my hosting account"</p>
                        
                        <h5 style="margin-top: 15px; margin-bottom: 10px;">Option 2: Edit Configuration Manually</h5>
                        <div class="code-box"># Find your Apache config file (usually one of these):
sudo nano /etc/apache2/sites-available/000-default.conf
# OR
sudo nano /etc/apache2/sites-available/default-ssl.conf

# Add this line to your &lt;VirtualHost&gt; section:
ServerAlias <?= htmlspecialchars($domain) ?> www.<?= htmlspecialchars($domain) ?>

# Example:
&lt;VirtualHost *:80&gt;
    ServerName redsharkpro.com
    ServerAlias www.redsharkpro.com <?= htmlspecialchars($domain) ?> www.<?= htmlspecialchars($domain) ?>

    DocumentRoot <?= $_SERVER['DOCUMENT_ROOT'] ?>

    # ... rest of config
&lt;/VirtualHost&gt;

# Save and restart Apache:
sudo systemctl restart apache2</div>
                    </div>
                    
                <?php elseif ($isNginx): ?>
                    <div class="warning-box">
                        <h4 style="margin-bottom: 10px;">🔧 Nginx Configuration</h4>
                        <p>You need to add your domain to the server block.</p>
                        
                        <h5 style="margin-top: 15px; margin-bottom: 10px;">Option 1: Contact Your Hosting Provider</h5>
                        <p>Tell them: "Please add <strong><?= htmlspecialchars($domain) ?></strong> as an alias/addon domain to my hosting account"</p>
                        
                        <h5 style="margin-top: 15px; margin-bottom: 10px;">Option 2: Edit Configuration Manually</h5>
                        <div class="code-box"># Find your Nginx config file (usually one of these):
sudo nano /etc/nginx/sites-available/default
# OR
sudo nano /etc/nginx/conf.d/default.conf

# Add your domain to the server_name line:
server {
    listen 80;
    listen [::]:80;
    server_name redsharkpro.com www.redsharkpro.com <?= htmlspecialchars($domain) ?> www.<?= htmlspecialchars($domain) ?>;

    root <?= $_SERVER['DOCUMENT_ROOT'] ?>;
    
    # ... rest of config
}

# Test configuration:
sudo nginx -t

# If OK, restart Nginx:
sudo systemctl restart nginx</div>
                    </div>
                    
                <?php else: ?>
                    <div class="warning-box">
                        <h4 style="margin-bottom: 10px;">🔧 Unknown Web Server</h4>
                        <p><strong>Best Option:</strong> Contact your hosting provider and ask them to add <strong><?= htmlspecialchars($domain) ?></strong> as an alias to your hosting account.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SSL Certificate -->
            <div class="step">
                <h3>🔒 Step 4: Install SSL Certificate (Optional but Recommended)</h3>
                <p>After configuring the web server, install an SSL certificate:</p>
                <div class="code-box"># Install Certbot (if not installed):
sudo apt install certbot python3-certbot-<?= $isApache ? 'apache' : 'nginx' ?>

# Get SSL certificate:
sudo certbot --<?= $isApache ? 'apache' : 'nginx' ?> -d <?= htmlspecialchars($domain) ?> -d www.<?= htmlspecialchars($domain) ?></div>
            </div>

            <!-- Testing -->
            <div class="success-box">
                <h2 style="margin-bottom: 15px;">🧪 Test Your Setup</h2>
                <ol style="line-height: 2;">
                    <li>Make sure DNS is propagated (green check above)</li>
                    <li>Configure your web server (Step 3)</li>
                    <li>Test by visiting: <a href="http://<?= htmlspecialchars($domain) ?>" target="_blank" style="color: #3b82f6;">http://<?= htmlspecialchars($domain) ?></a></li>
                    <li>Install SSL certificate (Step 4)</li>
                    <li>Test HTTPS: <a href="https://<?= htmlspecialchars($domain) ?>" target="_blank" style="color: #3b82f6;">https://<?= htmlspecialchars($domain) ?></a></li>
                </ol>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="?domain=<?= urlencode($domain) ?>" class="btn">🔄 Refresh Check</a>
                <a href="/" class="btn" style="background: #64748b; margin-left: 10px;">← Back to Dashboard</a>
            </div>

        </div>
    </div>
</body>
</html>
