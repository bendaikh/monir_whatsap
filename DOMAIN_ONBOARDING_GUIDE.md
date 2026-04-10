# Custom Domain Onboarding Guide

## Overview
Each store can have a custom domain in addition to its default subdomain. This allows you to use your own branded domain name (e.g., `www.mystore.com`) instead of the default subdomain (`mystore.yourdomain.com`).

## How to Setup a Custom Domain

### Step 1: Add Domain in Stores Management
1. Go to **Stores Management** dashboard
2. Find your store in the list
3. Click the **"Add Domain"** or **"Domain Setup"** button (purple/indigo button)
4. Enter your custom domain (e.g., `www.mystore.com` or `mystore.com`)
5. Click **"Save Domain"**

### Step 2: Configure DNS Records
After adding your domain, you must configure DNS records at your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.).

#### For www subdomain (e.g., www.mystore.com):
```
Type: CNAME
Name: www
Value: [your-subdomain].yourdomain.com
TTL: 3600 (or auto)
```

#### For root domain (e.g., mystore.com):
```
Type: A
Name: @ (or leave blank)
Value: [Your Server IP Address]
TTL: 3600 (or auto)
```

### Step 3: Wait for DNS Propagation
- DNS changes can take **24-48 hours** to fully propagate worldwide
- Your store remains accessible via the default subdomain during this time
- Use online tools like "DNS Checker" to verify propagation

### Step 4: Verify Domain
Once DNS propagates:
1. Visit your custom domain in a browser
2. Your store should load with all your products
3. Both domains will work:
   - Custom: `www.mystore.com`
   - Default: `mystore.yourdomain.com`

## Features

### In Stores Management
- **Domain Status Indicator** - Shows custom domain if configured
- **Add Domain Button** - Purple/indigo button with globe icon
- **DNS Instructions** - Modal shows exact DNS records to configure
- **Remove Domain** - Option to remove custom domain and use subdomain only

### Visual Indicators
When a custom domain is configured:
- Store listing shows: `www.mystore.com • mystore.yourdomain.com`
- Button changes from "Add Domain" to "Domain Setup"
- Purple/indigo color indicates domain feature

## Domain Management

### Update Domain
1. Click "Domain Setup" button
2. Change the domain name
3. Click "Save Domain"
4. Update DNS records at your registrar

### Remove Domain
1. Click "Domain Setup" button
2. Click "Remove Custom Domain" (red text at bottom left)
3. Confirm removal
4. Store will only be accessible via subdomain

## Technical Details

### Domain Validation
- Domains must be unique (no two stores can use the same domain)
- Domains are automatically cleaned (removes http://, https://, trailing slashes)
- Both `mystore.com` and `www.mystore.com` are valid

### Store Access
Once configured, customers can access your store via:
1. **Custom Domain**: `https://www.mystore.com`
2. **Default Subdomain**: `https://mystore.yourdomain.com`

Both URLs show the same store with the same products.

## Common DNS Registrars

### GoDaddy
1. Login to GoDaddy
2. Go to "My Products" → "DNS"
3. Click "Manage DNS"
4. Add the CNAME and A records

### Namecheap
1. Login to Namecheap
2. Go to "Domain List"
3. Click "Manage" next to your domain
4. Go to "Advanced DNS"
5. Add the records

### Cloudflare
1. Login to Cloudflare
2. Select your domain
3. Go to "DNS" section
4. Add the records
5. Note: Proxy status (orange cloud) should be enabled

## Troubleshooting

### Domain Not Working
- **Check DNS propagation**: Use https://dnschecker.org
- **Clear browser cache**: Try incognito/private mode
- **Wait longer**: DNS can take up to 48 hours
- **Verify DNS records**: Use `nslookup` or `dig` command

### "Domain already taken" Error
- Domain is already used by another store
- Check if you own this domain
- Contact support if you believe this is an error

### SSL Certificate Issues
- If using a custom domain, you may need to configure SSL
- Use Let's Encrypt or your hosting provider's SSL
- Cloudflare provides free SSL with proxy enabled

## Security Notes

1. **Ownership Verification** - Make sure you own the domain before adding it
2. **SSL Required** - Always use HTTPS for customer trust
3. **DNS Security** - Use DNSSEC if your registrar supports it
4. **Access Control** - Only the store owner can add/remove domains

## Best Practices

1. Use `www` subdomain (e.g., `www.mystore.com`) for better compatibility
2. Setup both root domain and www subdomain to redirect to one
3. Enable SSL/HTTPS before sharing your store
4. Test thoroughly before sharing with customers
5. Keep your domain registration up to date

## Example Configuration

### Before Domain Setup
- Store URL: `electronics.yourdomain.com`
- Customers must use this subdomain

### After Domain Setup
- Custom Domain: `www.myelectronics.com`
- Default Subdomain: `electronics.yourdomain.com`
- Both URLs work!

## Need Help?

If you encounter issues:
1. Check the DNS records are exactly as shown in the modal
2. Wait 24-48 hours for DNS propagation
3. Verify domain ownership
4. Contact your domain registrar support
5. Reach out to platform support
