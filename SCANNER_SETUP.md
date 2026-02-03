# Setup Scanner App untuk Public Access

## Perubahan yang Dilakukan

### 1. File dan Folder yang Dibuat

#### a. Scanner Web Application
- `/var/www/html/snipe/public/scanner-app/index.html` - Halaman utama aplikasi scanner
- `/var/www/html/snipe/public/scanner-app/app.js` - JavaScript untuk scanner logic
- `/var/www/html/snipe/public/scanner-app/style.css` - Styling aplikasi
- `/var/www/html/snipe/public/scanner-app/.htaccess` - Konfigurasi akses public
- `/var/www/html/snipe/public/scanner-app/README.md` - Dokumentasi

#### b. Backend API Controller
- `/var/www/html/snipe/app/Http/Controllers/Api/ScannerApiController.php` - Controller untuk Scanner API

#### c. Routes
- `/var/www/html/snipe/routes/scanner.php` - Route terpisah untuk Scanner API (public & protected)

### 2. Konfigurasi Apache

File: `/var/www/html/snipe/snipe.conf.new`

**Perubahan yang perlu diterapkan:**

```apache
<Directory /var/www/html/snipe/public>
    Options FollowSymLinks
    AllowOverride All        # Changed from None to All
    Require all granted      # Uncommented
</Directory>

# Tambahan untuk scanner-app
<Directory /var/www/html/snipe/public/scanner-app>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
    # No authentication required
</Directory>
```

**Cara Apply:**
```bash
sudo cp /var/www/html/snipe/snipe.conf.new /etc/apache2/sites-available/snipe.conf
sudo systemctl reload apache2
```

### 3. Route Service Provider

File: `/var/www/html/snipe/app/Providers/RouteServiceProvider.php`

**Perubahan:**
- Menambahkan method `mapScannerRoutes()` untuk load routes scanner tanpa auth middleware global
- Update method `boot()` untuk memanggil `mapScannerRoutes()`

### 4. API Routes

File: `/var/www/html/snipe/routes/scanner.php` (Baru)

**Public Routes (Tidak perlu auth):**
- `POST /api/v1/scanner/login` - Login dengan department
- `GET /api/v1/scanner/departments` - List departments

**Protected Routes (Perlu auth token):**
- `POST /api/v1/scanner/scan` - Scan asset by code
- `GET /api/v1/scanner/profile` - Get user profile

## Testing

### 1. Test Akses Web Scanner

```bash
curl -I https://uitrackin.ultid.com/scanner-app/
```

Expected: `HTTP/1.1 200 OK`

### 2. Test API Departments (Public)

```bash
curl -X GET "https://uitrackin.ultid.com/api/v1/scanner/departments" \
  -H "Accept: application/json"
```

Expected:
```json
{
  "success": true,
  "data": [...]
}
```

### 3. Test API Login (Public)

```bash
curl -X POST "https://uitrackin.ultid.com/api/v1/scanner/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "your_username",
    "password": "your_password",
    "department_id": 1
  }'
```

Expected (if valid):
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "..."
  }
}
```

### 4. Test API Scan (Protected)

```bash
TOKEN="your_bearer_token_from_login"
curl -X POST "https://uitrackin.ultid.com/api/v1/scanner/scan" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"code": "ASSET001"}'
```

## Akses Public - Konfirmasi

✅ **Scanner Web App**: `https://uitrackin.ultid.com/scanner-app/`
   - Bisa diakses tanpa VPN
   - Bisa diakses tanpa login domain
   - Halaman login terbuka untuk umum

✅ **API Login & Departments**: Public (tanpa auth)
   - Endpoint login terbuka untuk authentication
   - Endpoint departments untuk dropdown department

✅ **API Protected Endpoints**: Memerlukan Bearer Token
   - Scan asset
   - Get profile
   - Token didapat dari login response

## Security Considerations

1. **Rate Limiting**: 
   - Public endpoints: 60 requests/minute
   - Protected endpoints: 120 requests/minute

2. **HTTPS**: Aplikasi menggunakan SSL/TLS untuk enkripsi

3. **Token Authentication**: Protected endpoints menggunakan Laravel Passport Bearer Token

4. **Session**: Scanner app menggunakan localStorage untuk menyimpan token

5. **CORS**: Jika diperlukan, tambahkan domain ke `.env`:
   ```
   CORS_ALLOWED_ORIGINS=https://domain-lain.com
   ```

## Troubleshooting

### Jika API masih unauthorized:

1. Clear cache:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

2. Check route list:
```bash
php artisan route:list | grep scanner
```

3. Verify RouteServiceProvider loaded scanner routes

### Jika scanner app tidak bisa diakses:

1. Check file permissions:
```bash
ls -la /var/www/html/snipe/public/scanner-app/
```

2. Check Apache config:
```bash
sudo apache2ctl -t
sudo systemctl status apache2
```

3. Check Apache logs:
```bash
sudo tail -f /var/log/apache2/snipe_error.log
```

## Next Steps

### Untuk Development Mobile App

Gunakan API endpoints yang sama:
- Base URL: `https://uitrackin.ultid.com/api/v1/scanner/`
- Authentication: Bearer Token (dari login response)
- Framework pilihan: React Native, Flutter, Ionic, Native Android/iOS

### Contoh Flow:

1. User buka app → tampilkan login screen
2. User login → kirim POST ke `/scanner/login`
3. Simpan token di secure storage (AsyncStorage, SecureStore, etc)
4. User scan QR → kirim POST ke `/scanner/scan` dengan Bearer token
5. Tampilkan detail asset

## Maintenance

### Update Scanner App

Jika ada update pada scanner app, user perlu:
1. Clear browser cache
2. Atau hard refresh (Ctrl+Shift+R / Cmd+Shift+R)

### Update API

Jika ada perubahan pada API, jalankan:
```bash
php artisan route:clear
php artisan config:clear
```

## Support

Jika ada masalah, check:
1. Browser console (F12) untuk error JavaScript
2. Network tab untuk API response
3. Apache error logs
4. Laravel logs di `storage/logs/laravel.log`
