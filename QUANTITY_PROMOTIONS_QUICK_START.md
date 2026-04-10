# Quantity-Based Promotions - Quick Start

## What is this feature?

Set special prices when customers buy multiple items. Example:
- Buy 1: 100 MAD
- Buy 2-4: 90 MAD each
- Buy 5+: 80 MAD each

## How to use it (3 steps)

### 1. Enable Promotions
- Create/Edit product
- Find "Quantity-Based Pricing" card
- Toggle it ON

### 2. Add Pricing Tiers
- Click "Add Tier"
- Enter:
  - **Min Quantity**: e.g., 2
  - **Max Quantity**: e.g., 4 (or leave empty for unlimited)
  - **Price**: e.g., 90.00 MAD

### 3. Save
- Click "Create Product" or "Update Product"
- Done!

## Example Setup

**Product: T-Shirt**
- Regular Price: 100 MAD

**Promotions:**

**Tier 1:**
- Min: 2
- Max: 4
- Price: 90 MAD
- → Customers buying 2-4 items pay 90 MAD each (10% off)

**Tier 2:**
- Min: 5
- Max: (empty)
- Price: 80 MAD
- → Customers buying 5+ items pay 80 MAD each (20% off)

## Customer View

Customers will see a beautiful "Quantity Deals" section showing:
- All pricing tiers
- Discount percentages
- "Buy X-Y" quantity ranges
- Price per unit

## Tips

✓ Start with 2-3 tiers maximum
✓ Make discounts meaningful (5-10%+)
✓ Leave max_quantity empty for your highest tier
✓ Test different ranges based on your products

## Quick Troubleshooting

**Promotions not showing?**
1. Check if toggle is ON
2. Clear cache: `php artisan view:clear`
3. Refresh browser (Ctrl+Shift+R)

**Need help?**
See full guide: `QUANTITY_PROMOTIONS_GUIDE.md`

---

**Ready to boost your sales? Start adding promotions now!**
