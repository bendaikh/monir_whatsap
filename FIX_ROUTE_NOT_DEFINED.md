# Fix: Route Errors (product.show and product.submit-lead Not Defined)

## Issues
Getting multiple route errors:
1. `Route [product.show] not defined`
2. `Route [product.submit-lead] not defined`

## Root Cause
The application uses multi-tenant subdomain routing, so product-related routes require both the subdomain and the product slug as parameters:

```php
// Correct route definitions
Route::get('store/{subdomain}/product/{slug}', [ProductController::class, 'show'])
    ->name('store.product.show');

Route::post('store/{subdomain}/product/{slug}/submit-lead', [ProductController::class, 'submitLead'])
    ->name('store.product.submit-lead');
```

But the views were using incorrect route names without the `store.` prefix and missing the subdomain parameter.

## Solution

### 1. Fixed Product View Routes
Changed all instances of:
```blade
route('product.show', $product->slug)
```

To:
```blade
route('store.product.show', [$store->subdomain, $product->slug])
```

### 2. Fixed Submit Lead Routes
Changed:
```blade
route('product.submit-lead', $product->slug)
```

To:
```blade
route('store.product.submit-lead', [$store->subdomain, $product->slug])
```

### 3. Updated Controllers to Pass Store Variable
Updated controllers to pass the `$store` variable to views that need to link to products:

**CustomerDashboardController.php:**
- `products()` method - now passes `$store`
- `landingPageBuilder()` method - now passes `$store`

### 4. Added Conditional Checks
Since store might be null in some contexts, added `@if($store)` checks before generating links:

```blade
@if($store)
<a href="{{ route('store.product.show', [$store->subdomain, $product->slug]) }}" ...>
    View Product
</a>
@endif
```

## Files Modified

1. **resources/views/welcome.blade.php** (2 occurrences)
   - Updated product view links in featured and catalog sections

2. **resources/views/product-detail.blade.php** (1 occurrence)
   - Updated related products links

3. **resources/views/product-landing.blade.php** (1 occurrence)
   - Updated submit lead form action

4. **resources/views/customer/products.blade.php** (1 occurrence)
   - Updated "View Product Page" link with conditional check

5. **resources/views/customer/products-landing-builder.blade.php** (1 occurrence)
   - Updated "Preview Live" link with conditional check

6. **app/Http/Controllers/CustomerDashboardController.php**
   - Updated `products()` to load and pass store
   - Updated `landingPageBuilder()` to load and pass store

## Testing

After this fix:
- ✅ Product links on storefront work correctly
- ✅ "View Product" links in admin work correctly
- ✅ Related products links work correctly
- ✅ Preview links in landing page builder work correctly
- ✅ Lead submission forms work correctly
- ✅ Handles cases where store is not set (null checks)

## Route Structure

The application uses subdomain-based routing for multi-tenancy:

```
store.home                → /store/{subdomain}
store.product.show        → /store/{subdomain}/product/{slug}
store.product.submit-lead → /store/{subdomain}/product/{slug}/submit-lead

Example:
- Subdomain: myshop
- Product slug: premium-tshirt
- Product URL: /store/myshop/product/premium-tshirt
- Submit Lead URL: /store/myshop/product/premium-tshirt/submit-lead
```

This allows each store to have its own isolated product catalog accessible via their subdomain.

## All Store Routes

For reference, here are all the store-related routes:
- `store.home` - Store homepage
- `store.product.show` - Product detail page
- `store.product.submit-lead` - Lead submission endpoint
