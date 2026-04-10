# Quantity-Based Promotions - Visual Guide

## Admin Dashboard Views

### 1. Product Creation - Promotions Section

```
┌─────────────────────────────────────────────────────────────┐
│  💰 Quantity-Based Pricing                   [Toggle: ON]   │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ℹ️ How it works:                                            │
│  • Set different prices based on quantity purchased          │
│  • Example: Buy 1 for 100 MAD, Buy 2 for 90 MAD each        │
│  • Promotions apply automatically at checkout                │
│                                                               │
│  Pricing Tiers                            [+ Add Tier]       │
│                                                               │
│  ┌─────────────────────────────────────────────────┐ [X]    │
│  │ Min Quantity *   Max Quantity   Price (MAD) *   │        │
│  │ [    2     ]    [    4     ]    [  90.00   ]   │        │
│  │                                                  │        │
│  │ Example: Min: 2, Max: 4, Price: 90.00           │        │
│  │ → Customers buying 2-4 items pay 90 MAD each    │        │
│  └─────────────────────────────────────────────────┘        │
│                                                               │
│  ┌─────────────────────────────────────────────────┐ [X]    │
│  │ Min Quantity *   Max Quantity   Price (MAD) *   │        │
│  │ [    5     ]    [  (empty) ]    [  80.00   ]   │        │
│  │                                                  │        │
│  │ Example: Min: 5, Max: unlimited, Price: 80.00   │        │
│  │ → Customers buying 5+ items pay 80 MAD each     │        │
│  └─────────────────────────────────────────────────┘        │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### 2. Product Edit - Existing Promotions

```
┌─────────────────────────────────────────────────────────────┐
│  💰 Quantity-Based Pricing                   [Toggle: ON]   │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Pricing Tiers                            [+ Add Tier]       │
│                                                               │
│  ┌─────────────────────────────────────────────────┐ [X]    │
│  │ Min Quantity: [2] Max Quantity: [4]             │        │
│  │ Price: [90.00] MAD                              │        │
│  │                                                  │        │
│  │ Current: Min: 2, Max: 4, Price: 90.00 MAD      │        │
│  └─────────────────────────────────────────────────┘        │
│                                                               │
│  ┌─────────────────────────────────────────────────┐ [X]    │
│  │ Min Quantity: [5] Max Quantity: [(empty)]       │        │
│  │ Price: [80.00] MAD                              │        │
│  │                                                  │        │
│  │ Current: Min: 5, Max: unlimited, Price: 80.00   │        │
│  └─────────────────────────────────────────────────┘        │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## Customer-Facing Views

### 3. Product Landing Page - Promotions Display

```
┌─────────────────────────────────────────────────────────────┐
│                    PRODUCT LANDING PAGE                       │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────┐                                            │
│  │             │     Product Name                           │
│  │   IMAGE     │     [  100.00 MAD  ]                       │
│  │             │                                            │
│  └─────────────┘                                            │
│                                                               │
│  ╔═════════════════════════════════════════════════════╗    │
│  ║ 💰 Offres de Quantité / Quantity Deals             ║    │
│  ╠═════════════════════════════════════════════════════╣    │
│  ║                                                     ║    │
│  ║  ┌────────────┐  ┌────────────┐  ┌────────────┐   ║    │
│  ║  │ Achetez    │  │ Achetez    │  │ Achetez    │   ║    │
│  ║  │ 2 - 4      │  │ 5+         │  │ 10+        │   ║    │
│  ║  │  [-10%]    │  │  [-20%]    │  │  [-30%]    │   ║    │
│  ║  │            │  │            │  │            │   ║    │
│  ║  │ 90.00 MAD  │  │ 80.00 MAD  │  │ 70.00 MAD  │   ║    │
│  ║  │ par article│  │ par article│  │ par article│   ║    │
│  ║  └────────────┘  └────────────┘  └────────────┘   ║    │
│  ║                                                     ║    │
│  ╚═════════════════════════════════════════════════════╝    │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### 4. Product Detail Page - Promotions Display

```
┌─────────────────────────────────────────────────────────────┐
│                    PRODUCT DETAIL PAGE                        │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Product Name                                                │
│  100.00 MAD                                                  │
│                                                               │
│  ╔═════════════════════════════════════════════════════╗    │
│  ║ 💰 Special Quantity Pricing                         ║    │
│  ║ Save more when you buy more items                   ║    │
│  ╠═════════════════════════════════════════════════════╣    │
│  ║                                                      ║    │
│  ║  ┌────────────────┐  ┌────────────────┐            ║    │
│  ║  │ Buy 2 - 4      │  │ Buy 5+         │            ║    │
│  ║  │       [-10%]   │  │       [-20%]   │            ║    │
│  ║  │                │  │                │            ║    │
│  ║  │  90.00 MAD     │  │  80.00 MAD     │            ║    │
│  ║  │  per item      │  │  per item      │            ║    │
│  ║  └────────────────┘  └────────────────┘            ║    │
│  ║                                                      ║    │
│  ╚═════════════════════════════════════════════════════╝    │
│                                                               │
│  [Add to Cart]                                               │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## Color Scheme

### Admin Dashboard
- **Background**: Dark navy (#0f1c2e, #0a1628)
- **Borders**: White with transparency (border-white/10, border-white/20)
- **Accent**: Yellow/Gold (#f59e0b, #eab308)
- **Text**: White/Gray gradient
- **Cards**: Subtle gradient with backdrop blur

### Customer Pages

**Landing Page (Dark Theme)**:
- **Background**: Gradient from yellow-400/20 to orange-500/20
- **Borders**: Yellow-400/30
- **Cards**: White/10 with backdrop blur
- **Text**: White with various opacities
- **Badges**: Yellow-400 background, blue-900 text

**Detail Page (Light Theme)**:
- **Background**: Gradient from yellow-50 to orange-50
- **Borders**: Yellow-200, Yellow-300
- **Cards**: White with yellow borders
- **Text**: Gray-800, Gray-600, Gray-500
- **Badges**: Yellow-500 background, white text

## Interactive Elements

### Toggle Switch
```
OFF: ○─────  (Gray background)
ON:  ─────● (Yellow background)
```

### Add Tier Button
```
┌──────────────┐
│ + Add Tier   │  (Yellow background, white text)
└──────────────┘
```

### Remove Button (X)
```
[X]  (Red color, hover effect)
```

### Promotion Card Hover
```
Normal:  border-white/20
Hover:   border-yellow-400/50  (+ transform effect)
```

## Responsive Design

### Mobile (< 640px)
- Promotions: 1 column grid
- Cards stack vertically
- Full width buttons

### Tablet (640px - 1024px)
- Promotions: 2 columns grid
- Compact spacing

### Desktop (> 1024px)
- Promotions: 3 columns grid
- Maximum spacing and padding

## Icons Used

- 💰 Money/Coin icon: For promotions sections
- ℹ️ Info icon: For helper text
- ➕ Plus icon: For "Add Tier" button
- ✕ X icon: For remove buttons
- 📦 Package icon: For stock indicators

## Typography

### Admin Dashboard
- **Section Titles**: text-xl, font-bold
- **Labels**: text-xs, font-medium
- **Helper Text**: text-xs, text-gray-500
- **Prices**: text-sm to text-2xl depending on context

### Customer Pages
- **Section Titles**: text-2xl, font-bold
- **Quantity Labels**: text-sm, font-semibold
- **Prices**: text-2xl to text-4xl, font-black
- **Discount Badges**: text-xs to text-xl, font-black

## Animation & Transitions

1. **Toggle Switch**: Smooth slide animation (0.3s)
2. **Card Hover**: Border color transition (0.2s)
3. **Button Hover**: Background color transition (0.2s)
4. **Add/Remove Tiers**: Fade in/out (0.3s)

---

**Design Language**: Modern, clean, professional with emphasis on readability and clear call-to-actions. The yellow/gold color scheme emphasizes savings and promotions while maintaining a premium feel.
