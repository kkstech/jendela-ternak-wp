#!/bin/bash

# Export standard macOS installation directories to PATH
export PATH="/usr/local/bin:/opt/homebrew/bin:$PATH"

echo "==============================================="
echo " Starting Jendela Ternak WordPress Dev Server "
echo "==============================================="
echo ""

# 1. Pre-flight checks: Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed or not in your PATH."
    echo "Please install PHP (e.g., run: brew install php)"
    exit 1
fi

# 2. Pre-flight checks: Check if MySQL is running
if ! mysqladmin ping -u root &> /dev/null; then
    echo "⚠️  Warning: Local MySQL server does not seem to be running or is not accessible."
    echo "Please check if MySQL is running (e.g., run: brew services start mysql)"
    echo ""
fi

# 3. Define port and run server
PORT=8000
echo "🚀 Starting built-in PHP web server..."
echo "👉 Access WordPress at: http://127.0.0.1:$PORT"
echo "👉 Stop the server by pressing: Ctrl+C"
echo ""

# Start the built-in server WITH router.php for pretty permalink support.
# router.php mimics Apache mod_rewrite so URLs like /halaman-saya work
# instead of /index.php/halaman-saya
php -S 127.0.0.1:$PORT router.php
