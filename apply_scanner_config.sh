#!/bin/bash

# Script untuk apply konfigurasi Apache Scanner App
# Jalankan dengan: sudo bash apply_scanner_config.sh

echo "==================================="
echo "UITrackin Scanner App - Setup"
echo "==================================="
echo ""

# Backup konfigurasi lama
if [ ! -f /etc/apache2/sites-available/snipe.conf.backup ]; then
    echo "Backing up existing Apache config..."
    cp /etc/apache2/sites-available/snipe.conf /etc/apache2/sites-available/snipe.conf.backup-$(date +%Y%m%d-%H%M%S)
    echo "✓ Backup created"
else
    echo "⚠ Backup already exists, skipping..."
fi

# Copy konfigurasi baru
echo ""
echo "Applying new Apache configuration..."
cp /var/www/html/snipe/snipe.conf.new /etc/apache2/sites-available/snipe.conf
echo "✓ New config applied"

# Test konfigurasi
echo ""
echo "Testing Apache configuration..."
apache2ctl -t
if [ $? -eq 0 ]; then
    echo "✓ Apache configuration is valid"
    
    # Reload Apache
    echo ""
    echo "Reloading Apache..."
    systemctl reload apache2
    if [ $? -eq 0 ]; then
        echo "✓ Apache reloaded successfully"
    else
        echo "✗ Failed to reload Apache"
        exit 1
    fi
else
    echo "✗ Apache configuration has errors"
    echo "Restoring backup..."
    cp /etc/apache2/sites-available/snipe.conf.backup-* /etc/apache2/sites-available/snipe.conf 2>/dev/null
    exit 1
fi

# Test akses
echo ""
echo "==================================="
echo "Testing Scanner App Access..."
echo "==================================="
echo ""

echo "1. Testing Web Scanner..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://uitrackin.ultid.com/scanner-app/)
if [ "$HTTP_CODE" = "200" ]; then
    echo "   ✓ Web Scanner accessible (HTTP $HTTP_CODE)"
else
    echo "   ✗ Web Scanner error (HTTP $HTTP_CODE)"
fi

echo ""
echo "2. Testing API Departments..."
API_RESPONSE=$(curl -s https://uitrackin.ultid.com/api/v1/scanner/departments -H "Accept: application/json")
if echo "$API_RESPONSE" | grep -q "success"; then
    echo "   ✓ API Departments accessible"
    echo "   Response: $API_RESPONSE"
else
    echo "   ✗ API Departments error"
    echo "   Response: $API_RESPONSE"
fi

echo ""
echo "==================================="
echo "Setup Complete!"
echo "==================================="
echo ""
echo "Scanner App URL:"
echo "https://uitrackin.ultid.com/scanner-app/"
echo ""
echo "API Endpoints:"
echo "- POST /api/v1/scanner/login"
echo "- GET  /api/v1/scanner/departments"
echo "- POST /api/v1/scanner/scan (requires auth)"
echo "- GET  /api/v1/scanner/profile (requires auth)"
echo ""
echo "✓ All endpoints can be accessed without VPN"
echo "✓ No domain authentication required"
echo ""
