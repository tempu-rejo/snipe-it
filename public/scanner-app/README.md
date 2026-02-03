# UITrackin Scanner App

Aplikasi web untuk scanning dan tracking asset UITrackin menggunakan QR Code.

## 📋 Fitur

### 1. Login dengan Department
- Username / Email
- Password
- Pilihan Department

### 2. QR Code Scanner
- Scan QR Code menggunakan kamera device
- Input manual Asset Tag / ID
- Real-time scanning dengan Html5-QRCode library

### 3. Display Asset Details
- **Inv. Tag**: Nomor tag inventory
- **Serial**: Serial number asset
- **Name**: Nama asset
- **Category**: Kategori asset
- **Type**: Tipe/model asset
- **Brand**: Merek/manufacturer
- **Model**: Model number
- **WH / Office**: Lokasi warehouse/office
- **User Name**: Nama pengguna yang ditugaskan
- **Status**: Status asset
- **Purchase Date**: Tanggal pembelian
- **Notes**: Catatan tambahan
- **Image**: Gambar asset (jika ada)

## 🚀 Instalasi

### 1. Setup API di Snipe-IT

API endpoints sudah dibuat di:
- `app/Http/Controllers/Api/ScannerApiController.php`
- Routes di `routes/api.php`

### 2. Akses Aplikasi Scanner

Buka browser dan akses:
```
https://uitrackin.ultid.com/scanner-app/
```

### 3. Setup CORS (Jika Diperlukan)

Jika aplikasi di-host di domain terpisah, tambahkan domain ke `.env`:
```env
CORS_ALLOWED_ORIGINS=https://scanner.domain.com
```

## 📱 Cara Penggunaan

### Login
1. Buka aplikasi scanner
2. Masukkan username/email
3. Masukkan password
4. Pilih department
5. Klik "Login"

### Scanning Asset
1. Klik "Mulai Scan QR Code" untuk menggunakan kamera
2. Arahkan kamera ke QR Code asset
3. Atau masukkan kode manual di input field
4. Detail asset akan ditampilkan otomatis

### Logout
Klik tombol "Logout" di navbar untuk keluar

## 🔧 API Endpoints

### Public Endpoints (No Auth Required)

#### Login
```
POST /api/v1/scanner/login
```
Body:
```json
{
  "username": "user@example.com",
  "password": "password",
  "department_id": 1
}
```

Response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "username": "john",
      "email": "john@example.com",
      "first_name": "John",
      "last_name": "Doe",
      "department": {
        "id": 1,
        "name": "IT Department"
      }
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbG..."
  }
}
```

#### Get Departments
```
GET /api/v1/scanner/departments
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "IT Department"
    },
    {
      "id": 2,
      "name": "Finance"
    }
  ]
}
```

### Protected Endpoints (Requires Authentication)

#### Scan Asset
```
POST /api/v1/scanner/scan
Headers: Authorization: Bearer {token}
```
Body:
```json
{
  "code": "ASSET001" // Asset Tag or ID
}
```

Response:
```json
{
  "success": true,
  "data": {
    "inv_tag": "ASSET001",
    "serial": "SN123456",
    "name": "Dell Laptop",
    "category": "Laptops",
    "type": "Latitude 5420",
    "brand": "Dell",
    "model": "5420",
    "warehouse_office": "Jakarta Office",
    "user_name": "John Doe",
    "status": "Ready to Deploy",
    "purchase_date": "2023-01-15",
    "notes": "New laptop for IT department",
    "image": "https://uitrackin.ultid.com/uploads/assets/asset-image.jpg"
  }
}
```

#### Get Profile
```
GET /api/v1/scanner/profile
Headers: Authorization: Bearer {token}
```

## 🔐 Security

- Token-based authentication menggunakan Laravel Passport
- Token disimpan di localStorage browser
- Session timeout: 10 menit (sesuai konfigurasi)
- HTTPS required untuk production

## 📱 Mobile App Development

Untuk membuat mobile app (Android/iOS), gunakan framework:
- **React Native**: Cross-platform
- **Flutter**: Cross-platform
- **Ionic**: Cross-platform dengan web teknologi
- **Native**: Android (Kotlin/Java) atau iOS (Swift)

API endpoints sudah siap digunakan untuk mobile development.

### Contoh Integration dengan React Native

```javascript
// Login
const login = async (username, password, departmentId) => {
  const response = await fetch('https://uitrackin.ultid.com/api/v1/scanner/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      username,
      password,
      department_id: departmentId
    })
  });
  
  const result = await response.json();
  if (result.success) {
    // Save token
    await AsyncStorage.setItem('authToken', result.data.token);
  }
};

// Scan Asset
const scanAsset = async (code, token) => {
  const response = await fetch('https://uitrackin.ultid.com/api/v1/scanner/scan', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ code })
  });
  
  return await response.json();
};
```

## 🛠️ Troubleshooting

### Kamera tidak bisa diakses
- Pastikan browser memiliki permission untuk akses kamera
- Gunakan HTTPS (kamera tidak bisa diakses di HTTP)
- Gunakan browser modern (Chrome, Firefox, Safari)

### Login gagal
- Pastikan credentials benar
- Pastikan user terdaftar di department yang dipilih
- Check connection ke server

### Asset tidak ditemukan
- Pastikan QR Code berisi Asset Tag atau ID yang valid
- Check database apakah asset exist

## 📊 Browser Support

- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## 🔄 Update & Maintenance

### Clear Cache
Jika ada update aplikasi, user perlu clear browser cache atau refresh dengan Ctrl+F5

### Database Migration
Tidak ada migration khusus diperlukan, menggunakan struktur database Snipe-IT yang ada

## 📞 Support

Untuk bantuan lebih lanjut, hubungi IT Support atau check dokumentasi Snipe-IT di:
https://snipe-it.readme.io/

## 📝 License

Aplikasi ini menggunakan Snipe-IT sebagai backend.
Snipe-IT is licensed under the GNU Affero General Public License v3.0
