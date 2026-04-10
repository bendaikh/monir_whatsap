# Product Variations - Quick Start Guide

## Creating a Product with Variations

### Step 1: Enable Variations
When creating or editing a product, look for the "Product has variations" toggle:

```
┌─────────────────────────────────────────┐
│ □ Product has variations                │
│ Enable if this product comes in         │
│ different sizes, colors, or other       │
│ options                                 │
└─────────────────────────────────────────┘
```

### Step 2: Add Variations
Click the "Add Variation" button:

```
┌────────────────────────────────────────────┐
│  Product Variations           [+ Add Variation] │
├────────────────────────────────────────────┤
│                                            │
│  ┌─ Variation 1 ──────────────────[×]────┐│
│  │                                        ││
│  │  Attributes:                           ││
│  │  ┌─────────────┬─────────────┬───┐    ││
│  │  │ Color       │ Red         │ × │    ││
│  │  └─────────────┴─────────────┴───┘    ││
│  │  ┌─────────────┬─────────────┬───┐    ││
│  │  │ Size        │ Medium      │ × │    ││
│  │  └─────────────┴─────────────┴───┘    ││
│  │               [+ Add Attribute]        ││
│  │                                        ││
│  │  Price (MAD) *    Compare at Price    ││
│  │  ┌────────────┐   ┌────────────┐     ││
│  │  │ 99.00      │   │ 129.00     │     ││
│  │  └────────────┘   └────────────┘     ││
│  │                                        ││
│  │  Stock *          SKU                 ││
│  │  ┌────────────┐   ┌────────────┐     ││
│  │  │ 50         │   │ TSH-RED-M  │     ││
│  │  └────────────┘   └────────────┘     ││
│  │                                        ││
│  │  ☑ Default variation  ☑ Active       ││
│  └────────────────────────────────────────┘│
└────────────────────────────────────────────┘
```

### Step 3: Add Multiple Variations
Repeat for each variation of your product:

```
Example: T-Shirt Product

Variation 1:
- Attributes: Color: Red, Size: M
- Price: 99.00 MAD
- Stock: 50
- Default: Yes

Variation 2:
- Attributes: Color: Blue, Size: L
- Price: 109.00 MAD
- Stock: 30
- Default: No

Variation 3:
- Attributes: Color: Green, Size: S
- Price: 95.00 MAD
- Stock: 20
- Default: No
```

## Customer View

When customers visit your product page, they'll see:

```
┌──────────────────────────────────────────────┐
│  T-Shirt                                     │
│                                              │
│  Select Options:                             │
│                                              │
│  ○ Color: Red / Size: M                     │
│    99.00 MAD                                │
│    Stock: 50                                │
│                                              │
│  ● Color: Blue / Size: L (SELECTED)         │
│    109.00 MAD                               │
│    Stock: 30                                │
│                                              │
│  ○ Color: Green / Size: S                   │
│    95.00 MAD   -20%                         │
│    Stock: 20                                │
│                                              │
│  [Order Now]                                │
└──────────────────────────────────────────────┘
```

## Example Use Cases

### 1. Clothing Store
```
Product: Men's T-Shirt
- Variation 1: Small / Black
- Variation 2: Medium / Black
- Variation 3: Large / Black
- Variation 4: Small / White
- Variation 5: Medium / White
- Variation 6: Large / White
```

### 2. Electronics
```
Product: Wireless Headphones
- Variation 1: Black / Standard
- Variation 2: Black / Pro (with case)
- Variation 3: White / Standard
- Variation 4: White / Pro (with case)
```

### 3. Food/Beverage
```
Product: Premium Coffee
- Variation 1: 250g / Ground
- Variation 2: 250g / Whole Bean
- Variation 3: 500g / Ground
- Variation 4: 500g / Whole Bean
- Variation 5: 1kg / Whole Bean
```

### 4. Cosmetics
```
Product: Lipstick
- Variation 1: Matte / Red
- Variation 2: Matte / Pink
- Variation 3: Glossy / Red
- Variation 4: Glossy / Pink
- Variation 5: Glossy / Nude
```

## Tips for Best Results

### 1. Naming Attributes
✅ Good: `Color: Red`, `Size: Large`
❌ Bad: `Red`, `L` (without attribute names)

### 2. Price Strategy
- Set competitive prices for each variation
- Use "Compare at Price" to show discounts
- Consider volume pricing (larger sizes = better value)

### 3. Stock Management
- Keep stock updated for each variation
- Customers can see availability before ordering
- Total product stock = sum of all variation stocks

### 4. Default Variation
- Always mark one variation as default
- Choose your most popular or mid-range option
- This is what customers see first

### 5. Active/Inactive
- Use "Active" to control visibility
- Temporarily hide out-of-stock variations
- Keep inactive variations for future restocking

## Common Scenarios

### Scenario 1: Adding a New Color
1. Go to Edit Product
2. Scroll to "Product Variations"
3. Click "Add Variation"
4. Enter new color attributes and details
5. Save

### Scenario 2: Updating Prices
1. Go to Edit Product
2. Find the variation to update
3. Change the price field
4. Save

### Scenario 3: Removing Out-of-Stock Items
1. Go to Edit Product
2. Find the variation
3. Uncheck "Active" (or click × to delete)
4. Save

### Scenario 4: Changing Default Variation
1. Go to Edit Product
2. Uncheck "Default" on current default
3. Check "Default" on desired variation
4. Save

## API Integration

For developers integrating with external systems:

```php
// Get product with variations
$product = Product::with('activeVariations')->find($id);

// Access variations
foreach ($product->activeVariations as $variation) {
    echo $variation->attributes_display; // "Color: Red / Size: M"
    echo $variation->price; // 99.00
    echo $variation->stock; // 50
}

// Get price range
echo $product->price_range; // "95.00 - 109.00 MAD"

// Get total stock
echo $product->total_stock; // 100
```

## Troubleshooting

### Issue: Can't see variations on storefront
- Check that product "has_variations" is enabled
- Ensure at least one variation is marked as "Active"
- Verify the product itself is active

### Issue: Price not updating when selecting variation
- Clear browser cache
- Check JavaScript console for errors
- Ensure page fully loaded before selecting

### Issue: Default variation not showing
- Verify exactly one variation is marked as "Default"
- If multiple defaults exist, the first one is used

### Issue: Variations deleted when saving
- Ensure "has_variations" toggle is enabled
- Check that variation data is properly filled
- Verify required fields (price, stock) are set

## Need Help?

If you encounter any issues:
1. Check that all required fields are filled
2. Verify at least one variation has all required data
3. Ensure attributes have both name and value
4. Contact support with product ID and error details
