# Quantity-Based Promotions Feature Guide

## Overview

This feature allows you to set **quantity-based pricing** (also called bulk pricing or tiered pricing) for your products. You can offer discounts when customers buy multiple items.

## How It Works

When you enable promotions for a product, you define **pricing tiers** based on quantity ranges:

### Example:
- **Buy 1 item**: 100 MAD per item (regular price)
- **Buy 2-4 items**: 90 MAD per item (10% discount)
- **Buy 5+ items**: 80 MAD per item (20% discount)

The system automatically applies the best price based on the quantity the customer selects.

## Creating Promotions

### Step 1: Enable Promotions

1. Go to **Products** in your dashboard
2. Click **Create Product** (or edit an existing product)
3. Scroll to the **"Quantity-Based Pricing"** card
4. Toggle the switch to **enable** promotions

### Step 2: Add Pricing Tiers

1. Click the **"Add Tier"** button
2. For each tier, enter:
   - **Min Quantity**: Minimum quantity for this price (required)
   - **Max Quantity**: Maximum quantity (leave empty for unlimited)
   - **Price per Unit**: The price per item in MAD (required)

### Example Configuration:

**Tier 1:**
- Min Quantity: 2
- Max Quantity: 4
- Price: 90.00 MAD
- *Meaning*: Customers buying 2-4 items pay 90 MAD per item

**Tier 2:**
- Min Quantity: 5
- Max Quantity: (empty)
- Price: 80.00 MAD
- *Meaning*: Customers buying 5+ items pay 80 MAD per item

### Step 3: Save

Click **"Create Product"** or **"Update Product"** to save your promotions.

## Customer Experience

### On Product Pages

Customers will see a special **"Quantity Deals"** section displaying:
- All available pricing tiers
- Discount percentages (calculated automatically)
- Clear quantity ranges
- Price per unit for each tier

### Visual Highlights

- Promotions are displayed in an attractive **yellow/orange gradient** card
- Each tier shows:
  - "Buy X-Y" quantity range
  - Discount badge (e.g., "-10%")
  - Price per unit
  - "per item" label

### Multi-language Support

The promotions display adapts to your store's language settings:
- **French**: "Offres de Quantité", "Achetez", "par article"
- **English**: "Quantity Deals", "Buy", "per item"
- **Arabic**: "عروض الكمية", "اشتري", "لكل قطعة"

## Technical Details

### Database Structure

**Table: `product_promotions`**
- `id`: Primary key
- `product_id`: Foreign key to products
- `product_variation_id`: Optional (for variation-specific promotions)
- `min_quantity`: Minimum quantity (integer)
- `max_quantity`: Maximum quantity (nullable for unlimited)
- `price`: Price per unit (decimal)
- `promotion_type`: Type of promotion (default: 'quantity')
- `is_active`: Active status (boolean)
- `order`: Display order (integer)

### Model: `ProductPromotion`

**Key Methods:**
- `getQuantityRangeAttribute()`: Returns formatted range (e.g., "2 - 4" or "5+")
- `getDiscountPercentageAttribute()`: Calculates discount % from base price
- `product()`: Relationship to Product model
- `variation()`: Relationship to ProductVariation model

### Product Model Extensions

**New Field:**
- `has_promotions` (boolean): Whether product has promotions enabled

**New Methods:**
- `promotions()`: All promotions for the product
- `activePromotions()`: Only active promotions (ordered by min_quantity)
- `getPriceForQuantity($quantity, $variationId = null)`: Gets the best price for a given quantity

### Price Calculation Logic

```php
// Example: Get price for 3 items
$price = $product->getPriceForQuantity(3);

// With variations
$price = $product->getPriceForQuantity(3, $variationId);
```

The method finds the best matching promotion:
1. Filters by active status
2. Matches quantity range (min <= qty, max >= qty or null)
3. Orders by min_quantity descending (best deal first)
4. Falls back to base product/variation price if no promotion matches

## Editing Promotions

1. Go to **Products** > **Edit** for your product
2. Scroll to the **"Quantity-Based Pricing"** section
3. Existing promotions will be pre-loaded
4. You can:
   - Modify existing tiers
   - Add new tiers (click "Add Tier")
   - Remove tiers (click the X button)
5. Click **"Update Product"** to save changes

## Disabling Promotions

To disable promotions for a product:
1. Edit the product
2. Toggle the **"Quantity-Based Pricing"** switch to OFF
3. Save the product

**Note**: Disabling promotions will delete all existing promotion tiers for that product.

## Best Practices

### Pricing Strategy

1. **Start with 2-3 tiers maximum** to keep it simple for customers
2. **Make discounts meaningful**: At least 5-10% discount per tier
3. **Consider your margins**: Ensure profitability at all tier prices
4. **Test different ranges**: Start conservative and adjust based on sales data

### Quantity Ranges

1. **Don't overlap ranges**: Each tier should have distinct ranges
2. **Use logical jumps**: E.g., 2-4, 5-9, 10+
3. **Last tier unlimited**: Leave max_quantity empty for your highest tier
4. **Consider shipping**: Factor in shipping costs for bulk orders

### Marketing Tips

1. **Highlight savings**: The discount percentage is shown automatically
2. **Clear communication**: Explain the deals in your product description
3. **Create urgency**: "Buy more, save more!"
4. **Bundle products**: Encourage buying multiple items to reach higher tiers

## Troubleshooting

### Promotions not showing on product page

**Check:**
1. Is the "Quantity-Based Pricing" toggle enabled for the product?
2. Did you add at least one promotion tier?
3. Is the promotion's `is_active` field set to true?
4. Clear cache: `php artisan view:clear && php artisan cache:clear`

### Discount percentage showing as 0%

This happens when:
- The promotion price is equal to or higher than the base price
- The system calculates discount % from base price only
- **Fix**: Ensure promotion prices are lower than base price

### Prices not updating dynamically

- Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
- Check browser console for JavaScript errors
- Ensure JavaScript is enabled

## Advanced Features (Future)

### Variation-Specific Promotions

You can set different promotions for different variations:
- Red variant: Buy 3+ for 75 MAD
- Blue variant: Buy 2+ for 80 MAD

To implement:
```php
ProductPromotion::create([
    'product_id' => $product->id,
    'product_variation_id' => $variationId,
    'min_quantity' => 3,
    'price' => 75.00,
]);
```

### Time-Limited Promotions

Add start/end dates to promotions:
- Add `starts_at` and `ends_at` fields to the table
- Update validation logic to check date ranges

### Percentage-Based Discounts

Instead of fixed prices, use percentage discounts:
- Add `discount_type` field ('fixed' or 'percentage')
- Add `discount_value` field
- Calculate final price based on type

## Support

If you encounter any issues or have questions:
1. Check this guide first
2. Review the troubleshooting section
3. Contact support with:
   - Product ID
   - Steps to reproduce the issue
   - Screenshots if applicable

---

**Last Updated**: April 10, 2026
**Version**: 1.0
