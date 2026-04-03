#!/bin/bash

# Hostinger Node.js Setup Script
# Run this after uploading files to your nodejs folder

echo "==================================="
echo "WhatsApp Service - Hostinger Setup"
echo "==================================="

# Install dependencies
echo ""
echo "Installing dependencies..."
npm install

# Check if installation was successful
if [ $? -eq 0 ]; then
    echo "✓ Dependencies installed successfully"
else
    echo "✗ Failed to install dependencies"
    exit 1
fi

# Display status
echo ""
echo "Setup complete!"
echo ""
echo "Next steps:"
echo "1. Ensure PORT environment variable is set by Hostinger"
echo "2. Ensure LARAVEL_URL is set to your domain"
echo "3. Start the application from Hostinger Node.js control panel"
echo ""
echo "Test the service at: https://yourdomain.com/api/status"
echo ""
