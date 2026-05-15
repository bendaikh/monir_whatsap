# Quick Setup: Connect tanzaniaship.com to Your Store

## 🚀 3 Simple Steps

### Step 1: Configure DNS in Hostinger (5 minutes)

1. Go to https://hpanel.hostinger.com
2. Click **Domains** → Select **tanzaniaship.com** → **DNS / Name Servers**
3. Add these A records:

```
Record Type: A
Name: @
Points to: 178.16.128.28
TTL: 14400

Record Type: A  
Name: www
Points to: 178.16.128.28
TTL: 14400
```

4. Save changes

**Note:** DNS can take 1-24 hours to propagate (usually 1-6 hours)

---

### Step 2: Add Domain to Your Store

#### Option A: Via Web Interface (Easiest)

1. Login to https://redsharkpro.com
2. Go to **Stores** page
3. Click **Edit** on your Tanzania store
4. In the **Custom Domain** field, enter: `tanzaniaship.com`
5. Click **Update Store**

#### Option B: Via Database (Quick)

Connect to your database and run:

```sql
-- First, find your store ID:
SELECT id, name, subdomain FROM stores;

-- Then update with the domain (replace X with your store ID):
UPDATE stores SET domain = 'tanzaniaship.com' WHERE id = X;
```

---

### Step 3: Configure Web Server

Contact your hosting provider or update your Apache/Nginx config to accept `tanzaniaship.com`.

**For Apache**, add this ServerAlias to your VirtualHost:

```apache
ServerAlias tanzaniaship.com www.tanzaniaship.com
```

**For Nginx**, add to server_name:

```nginx
server_name redsharkpro.com www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com;
```

Then restart your web server:

```bash
# Apache
sudo systemctl restart apache2

# Nginx
sudo systemctl restart nginx
```

---

## ✅ Testing

### Check DNS (After Step 1)

```cmd
nslookup tanzaniaship.com
```

Should show: `178.16.128.28`

### Check Website (After All Steps)

1. Wait for DNS propagation (check at https://dnschecker.org)
2. Visit: https://tanzaniaship.com
3. Your store should appear!

---

## 🔒 SSL Certificate (Recommended)

SSH into your server and run:

```bash
# Install SSL certificate for both domains
sudo certbot --apache -d tanzaniaship.com -d www.tanzaniaship.com
# OR for Nginx:
sudo certbot --nginx -d tanzaniaship.com -d www.tanzaniaship.com
```

---

## 📋 Checklist

- [ ] DNS A records added in Hostinger (@ and www)
- [ ] Domain added to store in database
- [ ] Web server configured to accept the domain
- [ ] DNS propagated (check with nslookup or dnschecker.org)
- [ ] SSL certificate installed
- [ ] Test: Visit https://tanzaniaship.com

---

## ⚡ Quick Commands Reference

```bash
# Check if domain resolves
nslookup tanzaniaship.com

# Test DNS propagation
curl -I tanzaniaship.com

# Check web server config (Apache)
sudo apache2ctl -S

# Check web server config (Nginx)
sudo nginx -t

# Restart web server (Apache)
sudo systemctl restart apache2

# Restart web server (Nginx)
sudo systemctl restart nginx

# Check SSL certificate
openssl s_client -connect tanzaniaship.com:443 -servername tanzaniaship.com
```

---

## 🆘 Troubleshooting

| Issue | Solution |
|-------|----------|
| Domain not resolving | Wait for DNS propagation (up to 24h), clear DNS cache: `ipconfig /flushdns` |
| 404 Error | Check web server configuration, ensure ServerAlias/server_name includes the domain |
| Wrong store showing | Verify domain in database: `SELECT * FROM stores WHERE domain = 'tanzaniaship.com'` |
| SSL not working | Run certbot or enable free SSL in Hostinger hPanel |

---

**Need help?** Check the full guide: `DOMAIN_SETUP_GUIDE.md`
