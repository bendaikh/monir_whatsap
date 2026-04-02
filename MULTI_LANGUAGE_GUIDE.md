# Multi-Language Landing Page Feature

## Overview
Your product landing pages now support **French, English, and Arabic** with an attractive design and a contact form for lead generation.

## Features Implemented

### 1. Multi-Language Support
- **French (FR)** 🇫🇷
- **English (EN)** 🇬🇧
- **Arabic (AR)** 🇸🇦 with RTL support

### 2. Attractive Landing Page Design
- Beautiful gradient hero section with animated background
- Responsive product showcase
- Feature highlights with icons
- Photo gallery
- Smooth animations and transitions
- Modern glass-morphism effects

### 3. Contact Form
The landing page includes a contact form with:
- **Name** field
- **Phone number** field
- **Note** field (optional for comments/questions)
- Language detection (automatically saves which language the visitor used)
- Alternative contact methods (WhatsApp & Phone call buttons)

### 4. Lead Management Dashboard
- New "Leads" section in the dashboard
- View all form submissions with:
  - Date & time
  - Product name with thumbnail
  - Visitor name
  - Phone number (clickable to call or WhatsApp)
  - Language used
  - Additional notes

## How to Use

### Step 1: Configure AI Settings
1. Go to **Dashboard** → **AI Settings**
2. Add your OpenAI or Anthropic API key
3. Test the connection

### Step 2: Create a Product with Multi-Language Landing Page
1. Go to **Products** → **Create Product**
2. Fill in product details
3. **Enable "Generate AI Landing Page"** checkbox
4. Click **Create Product**

The AI will automatically generate:
- Hero title and description
- 4 feature highlights with icons
- Full product description
- Call-to-action buttons
- Contact form labels
- **All in 3 languages: French, English, and Arabic**

### Step 3: View Your Landing Page
1. After creating the product, go to your website
2. Click on the product
3. You'll see the new multi-language landing page with:
   - Language switcher at the top right
   - Beautiful hero section
   - Features section
   - Full description
   - Photo gallery
   - Contact form

### Step 4: Manage Leads
1. When visitors submit the contact form, they appear in **Dashboard** → **Products** → **Leads**
2. You can:
   - View all submissions
   - See which language they used
   - Call them directly
   - Message them on WhatsApp
   - View their questions/notes

## Database Changes
New tables and columns added:
- `product_leads` table - stores all form submissions
- `landing_page_fr`, `landing_page_en`, `landing_page_ar` columns in products table

## Routes Added
- `POST /product/{slug}/submit-lead` - Handle form submissions
- `GET /app/leads` - View leads dashboard

## Technical Details

### AI Content Structure
Each language version includes:
```json
{
  "hero_title": "Main headline",
  "hero_description": "Compelling description",
  "features": [
    {"title": "Feature", "description": "Details", "icon": "emoji"}
  ],
  "cta": "Call to action text",
  "full_description": "Detailed product info",
  "form_title": "Contact form title",
  "form_subtitle": "Form description",
  "form_name_placeholder": "Name field placeholder",
  "form_phone_placeholder": "Phone field placeholder",
  "form_note_placeholder": "Note field placeholder",
  "form_submit_button": "Submit button text"
}
```

### Language Switcher
- Uses Alpine.js for reactive switching
- No page reload needed
- Remembers user's language choice
- Automatically applies RTL for Arabic

## Next Steps
You can now:
1. Create products with AI-generated multi-language landing pages
2. Share product links with international customers
3. Collect leads through the contact form
4. Follow up with leads from the dashboard

Enjoy your new multi-language e-commerce platform! 🚀
