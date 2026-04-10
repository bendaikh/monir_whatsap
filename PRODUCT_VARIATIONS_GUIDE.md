# Product Variations Feature

This feature allows you to create products with multiple variations, such as different colors, sizes, or other attributes.

## Overview

Product variations enable you to:
- Offer the same product in different colors, sizes, or combinations
- Set different prices for each variation
- Manage individual stock levels for each variation
- Define custom attributes for each variation (e.g., Color: Red, Size: Large)

## Database Structure

### Tables Created

1. **product_variations** - Stores all product variations
   - `id` - Unique identifier
   - `product_id` - Foreign key to products table
   - `sku` - Stock Keeping Unit (optional)
   - `price` - Variation price
   - `compare_at_price` - Original price for discount display
   - `stock` - Available quantity
   - `attributes` - JSON field storing variation attributes (e.g., {"Color": "Red", "Size": "Large"})
   - `images` - JSON array of variation-specific images
   - `is_active` - Whether the variation is visible to customers
   - `is_default` - Marks the default variation shown initially
   - `order` - Sort order

2. **products** table updated with:
   - `has_variations` - Boolean flag indicating if product uses variations

## How to Use

### Creating a Product with Variations

1. Navigate to Products > Create New Product
2. Fill in basic product information (name, description, category)
3. Enable "Product has variations" toggle
4. Click "Add Variation" to create variation options
5. For each variation:
   - Add attributes (e.g., Color: Blue, Size: Medium)
   - Set price and stock
   - Optional: Set compare price, SKU
   - Mark one as "Default variation"
   - Ensure "Active" is checked

### Editing Product Variations

1. Go to Products > Edit Product
2. Toggle "Product has variations" if not already enabled
3. Existing variations will be displayed
4. You can:
   - Edit existing variations
   - Add new variations
   - Remove variations (with confirmation)
   - Update attributes, prices, and stock

### Customer-Facing Display

On the product detail page:
- If product has variations, customers see all active variations
- Each variation displays:
  - Attribute combination (e.g., "Color: Red / Size: Large")
  - Individual price
  - Stock availability
  - Discount percentage (if compare price is set)
- Customers can select a variation before ordering
- Default variation is pre-selected

## Model Relationships

### Product Model
```php
// Check if product has variations
$product->has_variations

// Get all variations
$product->variations

// Get only active variations
$product->activeVariations

// Get default variation
$product->defaultVariation

// Get price range (if has variations)
$product->price_range // Returns "99.00 - 149.00 MAD"

// Get total stock across all variations
$product->total_stock
```

### ProductVariation Model
```php
// Get parent product
$variation->product

// Get discount percentage
$variation->discount_percentage

// Get formatted attributes
$variation->attributes_display // Returns "Color: Red / Size: Large"

// Get first image (falls back to product image)
$variation->first_image
```

## API Examples

### Creating a Product with Variations
```php
$product = Product::create([
    'name' => 'T-Shirt',
    'has_variations' => true,
    'price' => 0, // Will be ignored when has_variations is true
    'stock' => 0, // Will be ignored
    // ... other fields
]);

// Create variations
ProductVariation::create([
    'product_id' => $product->id,
    'price' => 99.00,
    'stock' => 50,
    'attributes' => ['Color' => 'Red', 'Size' => 'M'],
    'is_default' => true,
    'is_active' => true,
]);

ProductVariation::create([
    'product_id' => $product->id,
    'price' => 109.00,
    'stock' => 30,
    'attributes' => ['Color' => 'Blue', 'Size' => 'L'],
    'is_active' => true,
]);
```

### Querying Products with Variations
```php
// Get all active products with their variations
$products = Product::with('activeVariations')
    ->where('is_active', true)
    ->get();

// Filter products by price range (considering variations)
$products = Product::whereHas('activeVariations', function($q) {
    $q->whereBetween('price', [50, 100]);
})->get();
```

## Notes

- When a product has variations enabled, the base product's price, stock, and SKU fields are ignored
- At least one variation should be marked as "default"
- If no default is explicitly set, the first variation becomes the default
- Variations can have their own images (planned for future enhancement)
- When variations are disabled on a product, all existing variations are deleted

## Future Enhancements

Potential improvements:
- Image gallery per variation
- Bulk import/export of variations via CSV
- Variation templates for common attribute combinations
- Inventory alerts per variation
- Sales analytics by variation
