# ⚠️ Masalah DNS - Domain Tidak Dapat Diakses dari Internet

## 🔍 Diagnosa

**Masalah:** Error `ERR_NAME_NOT_RESOLVED` ketika akses dari internet luar

**Penyebab:**
- Domain `uitrackin.ultid.com` hanya resolve di internal network (private DNS)
- DNS record hanya mengarah ke IP private: `10.25.10.128`
- Tidak ada public DNS record untuk domain ini
- Server memiliki IP public: `118.99.67.122`

**Bukti:**
```bash
# DNS public (Google DNS) - GAGAL
$ nslookup uitrackin.ultid.com 8.8.8.8
** server can't find uitrackin.ultid.com: NXDOMAIN

# DNS internal - SUKSES (tapi IP private)
$ nslookup uitrackin.ultid.com
Name: uitrackin.ultid.com
Address: 10.25.10.128
```

---

## ✅ Solusi 1: Setup Public DNS Record (RECOMMENDED)

Tambahkan DNS A Record di DNS Provider Anda:

### Langkah-langkah:

1. **Login ke DNS Management Portal** (misalnya Cloudflare, GoDaddy, Namecheap, dll)

2. **Tambahkan A Record:**
   ```
   Type: A
   Name: uitrackin
   Value: 118.99.67.122
   TTL: 300 (atau Auto)
   Proxy: Off (jika menggunakan Cloudflare)
   ```

3. **Tunggu DNS Propagation** (5-60 menit)

4. **Test:**
   ```bash
   nslookup uitrackin.ultid.com 8.8.8.8
   # Harus return: 118.99.67.122
   ```

### Setelah DNS Record Aktif:

URL akan berfungsi normal:
- ✅ `https://uitrackin.ultid.com/scanner-app/`
- ✅ Akses dari mana saja (internet public)
- ✅ Menggunakan SSL certificate yang sudah ada

---

## ✅ Solusi 2: Akses Langsung via IP Public (TEMPORARY)

Jika tidak bisa setup DNS segera, gunakan IP public langsung.

### Opsi A: HTTP (Tidak Secure - Hanya untuk Testing)

```
http://118.99.67.122/scanner-app/
```

⚠️ **Masalah:**
- Tidak ada SSL/HTTPS
- Certificate tidak match (untuk uitrackin.ultid.com)
- Browser akan warning
- Kamera tidak bisa diakses (butuh HTTPS)

### Opsi B: Setup Virtual Host untuk IP

Edit Apache config untuk accept request via IP:

```apache
<VirtualHost *:443>
    ServerName 118.99.67.122
    ServerAlias uitrackin.ultid.com
    DocumentRoot /var/www/html/snipe/public
    
    # ... konfigurasi lainnya sama
</VirtualHost>
```

Tapi tetap akan ada SSL certificate warning.

---

## ✅ Solusi 3: Setup Port Forwarding + Dynamic DNS (Jika di belakang NAT)

Jika server ada di belakang router/firewall:

### 1. Setup Port Forwarding di Router

Forward port 80 dan 443 dari router ke server internal:

```
External Port 443 → Internal IP 10.25.10.128:443
External Port 80  → Internal IP 10.25.10.128:80
```

### 2. Gunakan Dynamic DNS Service

Jika IP public berubah-ubah (dynamic):
- Daftar di: No-IP, DuckDNS, atau DynDNS
- Install DynDNS client di server
- Domain otomatis update ke IP public terbaru

---

## ✅ Solusi 4: Gunakan Subdomain dengan DNS Public

Jika domain `ultid.com` sudah di-manage public DNS:

### Tambahkan subdomain di public DNS:

```
Type: A
Name: uitrackin
Domain: ultid.com
Value: 118.99.67.122
TTL: 300
```

### Update Apache VirtualHost:

Tidak perlu diubah, sudah support `uitrackin.ultid.com`

### Update SSL Certificate (jika perlu):

```bash
# Jika menggunakan Let's Encrypt
sudo certbot --apache -d uitrackin.ultid.com
```

---

## 🔧 Quick Test - Akses Tanpa Domain

### Test API via IP (untuk development):

```bash
# Test departments endpoint
curl -k https://118.99.67.122/api/v1/scanner/departments \
  -H "Accept: application/json" \
  -H "Host: uitrackin.ultid.com"

# Test login endpoint
curl -k https://118.99.67.122/api/v1/scanner/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Host: uitrackin.ultid.com" \
  -d '{"username":"test","password":"test","department_id":1}'
```

Note: `-k` untuk skip SSL verification, `-H "Host: ..."` untuk virtual host routing

---

## 📋 Checklist Implementasi

### Untuk Akses dari Internet (Pilih Salah Satu):

- [ ] **Opsi 1 (Recommended):** Tambahkan public DNS A record untuk `uitrackin.ultid.com` → `118.99.67.122`
- [ ] **Opsi 2:** Setup port forwarding di router (jika ada)
- [ ] **Opsi 3:** Gunakan VPN untuk akses internal network
- [ ] **Opsi 4:** Deploy aplikasi di server dengan domain public yang sudah aktif

### Setelah DNS Aktif:

- [ ] Test akses: `https://uitrackin.ultid.com/scanner-app/`
- [ ] Test dari berbagai network (mobile data, wifi public, etc)
- [ ] Verify SSL certificate berfungsi
- [ ] Test scanner QR code (butuh HTTPS untuk kamera)

---

## 🔐 Catatan Keamanan

### Jika Expose ke Internet Public:

1. **Firewall:**
   ```bash
   # Hanya allow port 80 dan 443
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw enable
   ```

2. **Fail2Ban:**
   ```bash
   # Install fail2ban untuk block brute force
   sudo apt install fail2ban
   sudo systemctl enable fail2ban
   ```

3. **Rate Limiting:**
   - Sudah aktif di API: 60 req/min (public), 120 req/min (auth)

4. **SSL/TLS:**
   - Pastikan SSL certificate valid dan up-to-date
   - Gunakan Let's Encrypt untuk auto-renewal

5. **Apache Security Headers:**
   Edit [/etc/apache2/sites-available/snipe.conf](file:///etc/apache2/sites-available/snipe.conf):
   ```apache
   Header set X-Frame-Options "SAMEORIGIN"
   Header set X-Content-Type-Options "nosniff"
   Header set X-XSS-Protection "1; mode=block"
   Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
   ```

---

## 📞 Bantuan Lebih Lanjut

### Jika Tidak Punya Akses DNS Management:

Hubungi:
- IT Infrastructure team
- Domain administrator untuk ultid.com
- Hosting provider

### Informasi yang Perlu Diberikan:

```
Domain: uitrackin.ultid.com
Type: A Record
Value: 118.99.67.122
TTL: 300 (atau Auto)
```

---

## 🎯 Rekomendasi Akhir

**Untuk Production:**
1. ✅ Setup public DNS record (Solusi 1)
2. ✅ Pastikan SSL certificate valid
3. ✅ Enable firewall dan security measures
4. ✅ Monitor access logs
5. ✅ Setup backup dan disaster recovery

**Untuk Development/Testing:**
1. Gunakan VPN untuk akses internal
2. Atau test via IP dengan Host header
3. Atau deploy di server staging dengan domain public

---

## 📊 Status Saat Ini

| Component | Status | Notes |
|-----------|--------|-------|
| Server IP Public | ✅ `118.99.67.122` | Active |
| Server IP Private | ✅ `10.25.10.128` | Internal only |
| Apache Web Server | ✅ Running | Port 443 listening |
| Scanner App Files | ✅ Ready | All files created |
| API Endpoints | ✅ Working | Tested successfully |
| Public DNS | ❌ Not configured | **NEED TO FIX** |
| SSL Certificate | ✅ Installed | For uitrackin.ultid.com |

**Next Step:** Setup public DNS A record untuk `uitrackin.ultid.com` → `118.99.67.122`
