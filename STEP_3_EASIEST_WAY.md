# ⚡ STEP 3: Web Server Configuration - Easiest Method

## ✅ What You've Completed

- ✓ Step 1: DNS configured in Hostinger (A records added)
- ✓ Step 2: Domain added to your store in the database
- ⏳ Step 3: Need to configure web server ← **YOU ARE HERE**

---

## 🎯 The EASIEST Way (Recommended)

### Contact Your Hosting Provider

This is the simplest and safest method if you're not comfortable with server configuration.

**What to do:**

1. **Find your hosting provider's support contact:**
   - Check your hosting account dashboard
   - Look for "Support", "Live Chat", or "Help Desk"
   - Email or live chat support

2. **Send them this message:**

```
Hello,

I need to add a custom domain to my existing hosting account.

My current domain: redsharkpro.com
New domain to add: tanzaniaship.com

Please add "tanzaniaship.com" and "www.tanzaniaship.com" as 
aliases/addon domains to my hosting account so they point 
to the same location as redsharkpro.com.

The DNS is already configured to point to your server 
(IP: 178.16.128.28).

Thank you!
```

3. **Wait for their response** (usually 5-30 minutes)

4. **They'll configure it for you!** ✅

---

## 🔧 Advanced Method (If You Have SSH Access)

If you want to do it yourself and have SSH access:

### For Apache Users:

1. **Connect to your server via SSH**

2. **Edit your VirtualHost configuration:**
```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

3. **Find the ServerName line and add ServerAlias:**
```apache
<VirtualHost *:80>
    ServerName redsharkpro.com
    ServerAlias www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com
    
    DocumentRoot /path/to/your/laravel/public
    
    # ... rest of config stays the same
</VirtualHost>
```

4. **Save** (Ctrl+X, then Y, then Enter)

5. **Restart Apache:**
```bash
sudo systemctl restart apache2
```

6. **Done!** ✅

---

### For Nginx Users:

1. **Connect to your server via SSH**

2. **Edit your server block:**
```bash
sudo nano /etc/nginx/sites-available/default
```

3. **Add your domain to server_name:**
```nginx
server {
    listen 80;
    server_name redsharkpro.com www.redsharkpro.com tanzaniaship.com www.tanzaniaship.com;
    
    root /path/to/your/laravel/public;
    
    # ... rest of config stays the same
}
```

4. **Save** (Ctrl+X, then Y, then Enter)

5. **Test the configuration:**
```bash
sudo nginx -t
```

6. **If OK, restart Nginx:**
```bash
sudo nginx -t && sudo systemctl restart nginx
```

7. **Done!** ✅

---

## 🧪 Testing

### Method 1: Use the Server Checker Tool

1. Open your browser
2. Go to: **https://redsharkpro.com/check-server.php?domain=tanzaniaship.com**
3. This will show you:
   - ✓ If DNS is working
   - ✓ What web server you're using
   - ✓ Exact configuration instructions
   - ✓ Current status

### Method 2: Manual Testing

1. **Check DNS first:**
```bash
nslookup tanzaniaship.com
```
Should show: `178.16.128.28`

2. **After configuring web server, test in browser:**
   - Visit: http://tanzaniaship.com
   - Should show your store! 🎉

3. **If you see your store:**
   - Step 3 is complete! ✅
   - Move to Step 4 (SSL certificate)

---

## 🔒 Step 4: SSL Certificate (After Step 3 Works)

Once your domain is working over HTTP, add HTTPS:

```bash
# Install SSL certificate (free with Let's Encrypt)
sudo certbot --apache -d tanzaniaship.com -d www.tanzaniaship.com

# OR for Nginx:
sudo certbot --nginx -d tanzaniaship.com -d www.tanzaniaship.com
```

Then visit: **https://tanzaniaship.com** (with the 's') 🔒

---

## 🆘 Troubleshooting

### Issue: "DNS not resolving"
**Solution:** Wait longer (DNS takes 1-24 hours), clear your DNS cache:
```bash
# Windows
ipconfig /flushdns

# Mac/Linux
sudo dscacheutil -flushcache
```

### Issue: "Connection timed out"
**Solution:** 
- Web server not configured yet (do Step 3)
- Firewall blocking port 80/443
- Contact hosting provider

### Issue: "404 Not Found"
**Solution:**
- Web server is configured but domain not added as alias
- Check ServerAlias (Apache) or server_name (Nginx)

### Issue: "Shows different website"
**Solution:**
- Domain might be pointing to different hosting
- Check DNS: `nslookup tanzaniaship.com` (should show 178.16.128.28)

---

## 📋 Quick Checklist

```
✓ Step 1: DNS A records added (@ and www → 178.16.128.28)
✓ Step 2: Domain added to store in database
□ Step 3A: Contact hosting provider OR
□ Step 3B: Configure web server yourself
□ Test: Visit http://tanzaniaship.com
□ Step 4: Install SSL certificate
□ Test: Visit https://tanzaniaship.com
□ Celebrate! 🎉
```

---

## 💡 Pro Tips

1. **Best for beginners:** Contact your hosting provider (Step 3A)
2. **Use the checker tool:** Visit `/check-server.php` to see exactly what you need
3. **DNS takes time:** Be patient, it can take up to 24 hours
4. **SSL is important:** Always install SSL for security and trust
5. **Test both:** Make sure both `tanzaniaship.com` and `www.tanzaniaship.com` work

---

## 🎓 In Your Application

I've added a **Setup Guide button** in your store edit page!

To see it:
1. Go to: https://redsharkpro.com
2. Navigate to: **Stores** → **Edit** your store
3. Look for the **Custom Domain** field
4. Click the **"Setup Guide"** button
5. A detailed guide will appear with all steps!

---

## 📞 Need More Help?

1. **Server Checker Tool:** https://redsharkpro.com/check-server.php?domain=tanzaniaship.com
2. **In-app guide:** Stores → Edit → Custom Domain → "Setup Guide" button
3. **Hosting provider:** They can help with Step 3!

---

**Remember:** The easiest way is to just contact your hosting provider and ask them to add the domain as an alias. They do this all the time and it takes them 5 minutes! 😊

Good luck! 🚀
