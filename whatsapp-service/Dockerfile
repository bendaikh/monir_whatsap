# Use Node.js 18 with Chrome pre-installed
FROM ghcr.io/puppeteer/puppeteer:21.6.1

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Set Puppeteer to skip download (use bundled Chromium)
ENV PUPPETEER_SKIP_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome-stable

# Install dependencies
RUN npm ci --only=production

# Copy application files
COPY . .

# Expose port (Railway will override this)
EXPOSE 3000

# Start the application
CMD ["node", "server.js"]
