# AI Campaign Creator

## Overview

The AI Campaign Creator is a powerful feature that helps you create and publish advertising campaigns to Facebook and TikTok with AI assistance. Simply provide your product details, and let AI generate compelling ad copy for you.

## Features

### 🤖 AI-Powered Copywriting
- Generate attention-grabbing headlines
- Create compelling primary ad text
- Write concise descriptions
- Generate effective call-to-action phrases

### 🎯 Multi-Platform Support
- Publish to Facebook Ads
- Publish to TikTok Ads
- Support for multiple ad accounts
- Cross-platform campaign creation

### 🎨 Customization Options
- Multiple tone options (Professional, Casual, Exciting, Urgent, Friendly)
- Campaign objectives (Awareness, Consideration, Conversion)
- Flexible budget settings
- Target audience specification

## Setup Requirements

### 1. Configure Your AI Settings

The AI Campaign Creator uses your existing OpenAI API integration:

1. Navigate to **AI API Integration > OpenAI Connect**
2. If you haven't already, add your OpenAI API key:
   - Get your API key from [OpenAI Platform](https://platform.openai.com/api-keys)
   - Enter the key in the OpenAI settings form
   - Select your preferred model (GPT-4 recommended for best results)
   - Save your settings
3. The Campaign Creator will automatically use your configured API key

**Note**: The AI features use your user-specific OpenAI settings, not a global environment variable.

### 2. Connected Ad Accounts

Before creating campaigns, you must connect your ad accounts:

#### Facebook Ads
1. Navigate to **Social Media API > Facebook Ads Connect**
2. Generate an access token from [Facebook Access Token Tool](https://developers.facebook.com/tools/accesstoken/)
3. Ensure your token includes these permissions:
   - `ads_read`
   - `ads_management`
   - `business_management`
4. Enter your Ad Account ID (format: `act_XXXXXXXXXX`)
5. Save the connection

#### TikTok Ads
1. Navigate to **Social Media API > TikTok Ads Connect**
2. Generate an access token from [TikTok Ads Manager](https://ads.tiktok.com/marketing_api/auth)
3. Enter your Advertiser ID
4. Save the connection

## How to Use

### Step 1: Ensure OpenAI is Configured

Before using the AI Campaign Creator:
1. Go to **AI API Integration > OpenAI Connect** 
2. Make sure your OpenAI API key is saved
3. The Campaign Creator will use this key for AI generation

### Step 2: Access the Campaign Creator

Navigate to **Social Media API > Create Campaign (AI)** or click the **"Create with AI"** button on the Campaigns Dashboard.

### Step 3: Fill Product Information

1. **Product/Service Name**: Enter the name of what you're advertising
2. **Product Description**: Describe your product, its features, and benefits
3. **Target Audience** (Optional): Specify who your ideal customers are

Example:
```
Product Name: Premium Wireless Headphones
Description: High-quality noise-canceling headphones with 30-hour battery life, 
crystal-clear sound, and comfortable design perfect for music lovers and professionals.
Target Audience: Tech-savvy millennials aged 25-40 who value quality audio
```

### Step 4: Configure Campaign Settings

1. **Campaign Name**: Give your campaign a memorable name
2. **Campaign Objective**: Choose your goal
   - **Brand Awareness**: Build recognition and reach
   - **Traffic & Engagement**: Drive clicks and interactions
   - **Conversions & Sales**: Generate purchases and leads
3. **Daily Budget**: Set your daily spending limit (in USD)
4. **Tone**: Select the voice for your AI-generated copy

### Step 5: Generate AI Copy

For each content field, click the **"Generate with AI"** button:

1. **Headline**: Short, attention-grabbing title (40 characters max)
2. **Primary Text**: Main ad copy (125 words max) - **REQUIRED**
3. **Description**: Supporting information (30 words max)
4. **Call to Action**: Action phrase (5 words max)

**Tip**: You can regenerate content multiple times until you find the perfect copy!

### Step 6: Select Platforms

1. Check the platforms you want to publish to (Facebook, TikTok, or both)
2. Select the specific ad account for each platform
3. You can publish to multiple platforms simultaneously

### Step 7: Create Campaign

Click **"Create Campaign"** to publish your campaign. The campaigns will be created in **PAUSED** status, allowing you to:
- Review the campaign in your Facebook/TikTok Ads Manager
- Add creatives (images/videos)
- Fine-tune settings
- Activate when ready

## Best Practices

### Writing Effective Product Descriptions
- Focus on benefits, not just features
- Be specific about what makes your product unique
- Include key selling points
- Mention your target market

### Choosing the Right Tone
- **Professional**: B2B products, corporate services
- **Casual**: Everyday consumer products, lifestyle brands
- **Exciting**: New products, limited offers, entertainment
- **Urgent**: Sales, promotions, time-sensitive offers
- **Friendly**: Community-focused, personal services

### Budget Recommendations
- **Facebook Ads**: Minimum $5-10/day recommended
- **TikTok Ads**: Minimum $20/day at campaign level
- Start small and scale based on performance

### Platform-Specific Tips

#### Facebook Ads
- Primary text works best at 125 characters or less
- Headlines should be 27 characters for optimal display
- Test multiple ad variations

#### TikTok Ads
- Keep copy concise and mobile-friendly
- Focus on visual storytelling
- Use trending formats and styles

## Campaign Management

After creation, manage your campaigns:

1. Go to **Social Media API > Campaigns Dashboard**
2. View all campaigns across platforms
3. Monitor performance metrics:
   - Spend
   - Impressions
   - Clicks
   - Conversions
   - CTR, CPC, CPM
4. Click **"Refresh Data"** to update metrics

## Troubleshooting

### "OpenAI API key not configured"
- Go to **AI API Integration > OpenAI Connect**
- Add your OpenAI API key
- Save your settings
- Return to the Campaign Creator

### "Unable to decrypt your API key"
- Your stored API key may be corrupted
- Go to AI Settings and re-enter your OpenAI API key
- Save and try again

### "No Ad Accounts Connected"
- Connect at least one ad account (Facebook or TikTok)
- Ensure your access token has not expired

### "Facebook Access Token has expired"
- Go to Facebook Ads Connect
- Generate a new access token
- Update your connection

### "Failed to create campaign"
- Check that your ad account has proper permissions
- Verify your budget meets minimum requirements
- Ensure your access token is valid

## API Integration Details

### Facebook Marketing API
- API Version: v18.0
- Creates campaigns via Graph API
- Campaigns created in PAUSED status
- Requires additional setup for ad sets and ads

### TikTok Marketing API
- API Version: v1.3
- Creates campaigns via Business API
- Campaigns created in DISABLED status
- Requires additional setup for ad groups and ads

## Costs

### OpenAI API Costs
- GPT-4 pricing applies for AI generation
- Typical cost per generation: $0.01-0.05
- Charged directly by OpenAI based on usage

### Ad Platform Costs
- You control all advertising budgets
- Billed directly by Facebook/TikTok
- No additional fees from our platform

## Security

- All access tokens are encrypted in the database
- API keys are never exposed to the frontend
- Secure HTTPS communication with all platforms
- Token expiration monitoring and notifications

## Future Enhancements

Planned features:
- Image/video creative generation
- A/B testing automation
- Performance optimization suggestions
- Audience targeting recommendations
- Campaign templates library
- Bulk campaign creation

## Support

For issues or questions:
1. Check the error message displayed
2. Review your ad account settings
3. Verify API credentials and permissions
4. Check platform-specific documentation:
   - [Facebook Marketing API Docs](https://developers.facebook.com/docs/marketing-api)
   - [TikTok Marketing API Docs](https://business-api.tiktok.com/portal/docs)

## Limitations

### Current Limitations
- Creates campaign structure only (not ad sets/ads)
- No image/video upload support yet
- Limited to standard campaign objectives
- Requires manual activation in platform managers

### Platform Limitations
- **Facebook**: Requires Business Manager setup
- **TikTok**: Requires approved developer app
- Both platforms have review processes for new ads

---

**Version**: 1.0.0  
**Last Updated**: April 2026
