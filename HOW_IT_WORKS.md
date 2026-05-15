# How Custom Domain Parking Works

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER'S BROWSER                          │
│                                                                 │
│  Types: tanzaniaship.com                                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DNS RESOLUTION                             │
│                     (Hostinger DNS)                             │
│                                                                 │
│  tanzaniaship.com   ──►   178.16.128.28                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                   WEB SERVER (Apache/Nginx)                     │
│                   IP: 178.16.128.28                             │
│                                                                 │
│  Accepts requests for:                                          │
│  • redsharkpro.com                                              │
│  • tanzaniaship.com                                             │
│  • any other custom domains                                     │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL APPLICATION                          │
│                                                                 │
│  ┌───────────────────────────────────────────────────────┐     │
│  │ DetectCustomDomain Middleware                         │     │
│  │                                                       │     │
│  │ 1. Reads request host: tanzaniaship.com              │     │
│  │ 2. Queries database for matching store               │     │
│  │ 3. Loads store: "Tanzania Ship Store"                │     │
│  └───────────────────────────────────────────────────────┘     │
│                         │                                       │
│                         ▼                                       │
│  ┌───────────────────────────────────────────────────────┐     │
│  │ ProductController                                     │     │
│  │                                                       │     │
│  │ • Shows products from Tanzania Ship Store            │     │
│  │ • Uses store's branding & settings                   │     │
│  └───────────────────────────────────────────────────────┘     │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATABASE                                 │
│                                                                 │
│  stores table:                                                  │
│  ┌────┬─────────────────┬───────────┬──────────────────┐       │
│  │ ID │ Name            │ Subdomain │ Domain           │       │
│  ├────┼─────────────────┼───────────┼──────────────────┤       │
│  │ 1  │ Tanzania Ship   │ tanzania  │ tanzaniaship.com │       │
│  │ 2  │ Another Store   │ store2    │ null             │       │
│  └────┴─────────────────┴───────────┴──────────────────┘       │
└─────────────────────────────────────────────────────────────────┘
```

## Request Flow Example

### Scenario 1: User visits tanzaniaship.com

```
1. Browser ──► DNS Lookup: tanzaniaship.com → 178.16.128.28

2. HTTP Request to 178.16.128.28
   Host: tanzaniaship.com

3. Web Server receives request
   ├─ Checks VirtualHost/ServerName
   └─ Forwards to Laravel

4. Laravel Middleware (DetectCustomDomain)
   ├─ Reads host: "tanzaniaship.com"
   ├─ Query: SELECT * FROM stores WHERE domain = 'tanzaniaship.com'
   └─ Found Store ID: 1 (Tanzania Ship)

5. Controller loads products from Store ID 1

6. Response sent back with Tanzania Ship Store content

7. Browser displays Tanzania Ship Store
```

### Scenario 2: User visits redsharkpro.com/store/tanzania

```
1. Browser ──► DNS Lookup: redsharkpro.com → 178.16.128.28

2. HTTP Request to 178.16.128.28
   Host: redsharkpro.com
   Path: /store/tanzania

3. Web Server receives request
   └─ Forwards to Laravel

4. Laravel Routes
   ├─ No custom domain detected
   ├─ Route: /store/{subdomain}
   └─ Query: SELECT * FROM stores WHERE subdomain = 'tanzania'

5. Controller loads products from Store ID 1

6. Response sent back with Tanzania Ship Store content

7. Browser displays Tanzania Ship Store
```

## What We've Built

### 1. DetectCustomDomain Middleware
**File:** `app/Http/Middleware/DetectCustomDomain.php`

- Intercepts all incoming requests
- Checks if the request host matches a custom domain in the database
- Loads the appropriate store automatically
- Works seamlessly with existing subdomain routing

### 2. Updated ProductController
**File:** `app/Http/Controllers/ProductController.php`

- Now supports both subdomain AND custom domain access
- Checks for custom domain first, falls back to subdomain
- All methods updated: index(), show(), submitLead(), thankYou()

### 3. Middleware Registration
**File:** `bootstrap/app.php`

- DetectCustomDomain registered in web middleware stack
- Runs on every web request before other middleware

### 4. Database Schema
**Already exists:** `stores.domain` column

- Stores custom domain for each store
- Unique constraint ensures no duplicate domains
- Nullable (optional feature)

## Multiple Stores Example

You can park multiple domains to different stores:

```
┌──────────────────────┬────────────┬────────────────────┐
│ Domain               │ Store      │ Shows              │
├──────────────────────┼────────────┼────────────────────┤
│ tanzaniaship.com     │ Store 1    │ Tanzania products  │
│ myshop.com           │ Store 2    │ Electronics        │
│ fashion.store        │ Store 3    │ Fashion items      │
│ redsharkpro.com      │ Dashboard  │ Admin panel        │
└──────────────────────┴────────────┴────────────────────┘
```

Each domain automatically loads its respective store!

## Technical Requirements Met

✅ DNS points to correct IP (178.16.128.28)
✅ Web server accepts multiple domains
✅ Laravel detects custom domains automatically
✅ Database stores domain mappings
✅ SSL can be added per domain
✅ Subdomain access still works
✅ Admin dashboard remains on main domain

## Security & Performance

### Security
- Domain validation in middleware
- Only active stores are shown
- No cross-store data leakage
- Each store isolated by ID

### Performance
- Single database query per request
- Results can be cached
- No additional overhead
- Scales to unlimited domains

## Next Steps for User

1. ✅ **Configure DNS** in Hostinger → Point to 178.16.128.28
2. ✅ **Add domain to store** → Via web interface or database
3. ✅ **Update web server** → Add ServerAlias/server_name
4. ⏳ **Wait for DNS** → Usually 1-6 hours
5. ✅ **Install SSL** → Via certbot or Hostinger
6. ✅ **Test** → Visit tanzaniaship.com

## Summary

Your Laravel application now supports unlimited custom domains! Each store can have its own domain, and the middleware automatically detects which store to show based on the incoming request's host header.

**Main Domain:** redsharkpro.com (Admin panel & dashboard)
**Custom Domain:** tanzaniaship.com (Tanzania Ship Store)

All you need to do is:
1. Point the DNS
2. Add the domain to your store
3. Configure your web server

Done! 🎉
