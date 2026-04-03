#!/bin/sh

# Find Chromium executable
if command -v chromium >/dev/null 2>&1; then
    export PUPPETEER_EXECUTABLE_PATH=$(command -v chromium)
    echo "Found Chromium at: $PUPPETEER_EXECUTABLE_PATH"
elif command -v chromium-browser >/dev/null 2>&1; then
    export PUPPETEER_EXECUTABLE_PATH=$(command -v chromium-browser)
    echo "Found Chromium at: $PUPPETEER_EXECUTABLE_PATH"
elif [ -f "/nix/store/"*"chromium"*"/bin/chromium" ]; then
    export PUPPETEER_EXECUTABLE_PATH=$(find /nix/store -name chromium -type f -executable 2>/dev/null | head -n1)
    echo "Found Chromium at: $PUPPETEER_EXECUTABLE_PATH"
else
    echo "ERROR: Chromium not found!"
    echo "Searching in /nix/store..."
    find /nix/store -name "*chromium*" -type f 2>/dev/null | head -20
fi

# Start the Node.js server
exec node server.js
