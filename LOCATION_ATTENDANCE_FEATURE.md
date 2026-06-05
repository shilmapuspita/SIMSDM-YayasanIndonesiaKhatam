# 📍 FITUR ABSENSI BERBASIS LOKASI - DOKUMENTASI LENGKAP

## 📋 Ringkasan Fitur

Fitur absensi berbasis lokasi memastikan karyawan hanya dapat melakukan absensi (check-in/check-out) jika berada dalam jarak maksimal 30 meter dari kantor.

**Fitur Utama:**

- ✅ Validasi lokasi real-time menggunakan Geolocation API
- ✅ Haversine formula untuk menghitung jarak akurat
- ✅ Validasi backend yang ketat (tidak bisa dimanipulasi)
- ✅ Simpan latitude, longitude, dan jarak di database
- ✅ Tampilan real-time jarak dari kantor
- ✅ Status badge: "Di dalam area kantor" atau "Di luar area kantor"
- ✅ Tombol disabled jika lokasi tidak valid
- ✅ Configurable via .env

---

## 🔧 FILE YANG TELAH DIBUAT/DIMODIFIKASI

### 1. **Migration (NEW)**

📁 `database/migrations/2026_05_08_000000_add_location_to_attendances_table.php`

Menambahkan kolom:

- `check_in_latitude` - Latitude saat check-in
- `check_in_longitude` - Longitude saat check-in
- `check_in_distance` - Jarak dari kantor saat check-in
- `check_out_latitude` - Latitude saat check-out
- `check_out_longitude` - Longitude saat check-out
- `check_out_distance` - Jarak dari kantor saat check-out

### 2. **Helper Utility (NEW)**

📁 `app/Helpers/LocationHelper.php`

Fungsi-fungsi:

- `haversineDistance()` - Hitung jarak antara 2 koordinat
- `isWithinOfficeRadius()` - Cek apakah user dalam radius kantor
- `getDistanceToOffice()` - Ambil jarak ke kantor
- `isValidCoordinate()` - Validasi koordinat

### 3. **Config (NEW)**

📁 `config/attendance.php`

Konfigurasi:

- `office_latitude` - Latitude kantor (dari .env)
- `office_longitude` - Longitude kantor (dari .env)
- `office_radius` - Radius dalam meter (dari .env)
- `check_in_time` - Waktu masuk standar (dari .env)
- `check_out_time` - Waktu pulang standar (dari .env)

### 4. **Environment Variables (UPDATED)**

📁 `.env`

Ditambahkan:

```env
OFFICE_LATITUDE=-6.200000
OFFICE_LONGITUDE=106.816667
OFFICE_RADIUS=30
```

**⚠️ PENTING: Ganti koordinat dengan lokasi kantor Anda!**

- Gunakan Google Maps untuk mendapatkan koordinat yang akurat
- Format: OFFICE_LATITUDE=longitude (dengan titik desimal)

### 5. **Model Attendance (UPDATED)**

📁 `app/Models/Attendance.php`

Updated:

- Tambah kolom baru ke `$fillable`
- Siap untuk menyimpan data lokasi

### 6. **Controller (UPDATED)**

📁 `app/Http/Controllers/EmployeeDashboardController.php`

Updated methods:

- `storeAbsensi()` - Validasi lokasi + simpan check-in dengan data lokasi
- `updateAbsensi()` - Validasi lokasi + simpan check-out dengan data lokasi

**Validasi Backend:**

```php
// ✅ Validasi koordinat user
// ✅ Validasi dalam radius kantor
// ✅ Return JSON response (bukan redirect)
// ✅ Reject jika di luar radius
```

### 7. **View (UPDATED)**

📁 `resources/views/employee/absensi.blade.php`

Updated:

- ✅ Geolocation API integration
- ✅ Real-time distance display
- ✅ Location status badge
- ✅ AJAX submission
- ✅ Loading indicators
- ✅ Error handling
- ✅ Table dengan kolom distance

---

## 🚀 SETUP INSTRUCTIONS

### Step 1: Update Koordinat Kantor

Buka `.env` dan ganti dengan koordinat kantor Anda:

```env
# Google Maps → Klik pada lokasi kantor → Copy koordinat
OFFICE_LATITUDE=-6.200000
OFFICE_LONGITUDE=106.816667
OFFICE_RADIUS=30
```

**Cara mendapatkan koordinat:**

1. Buka Google Maps
2. Klik pada lokasi kantor
3. Klik pada koordinat di paling atas
4. Koordinat akan ter-copy otomatis

### Step 2: Run Migration

```bash
php artisan migrate
```

Jika ingin rollback:

```bash
php artisan migrate:rollback
```

### Step 3: Test Fitur

1. Login sebagai user
2. Buka menu Absensi
3. Browser akan meminta izin akses lokasi
4. Berikan izin akses
5. Sistem akan menampilkan jarak realtime
6. Button akan aktif jika berada dalam radius

---

## 📌 BAGAIMANA FITUR BEKERJA

### Frontend Flow:

```
1. User buka halaman absensi
   ↓
2. Browser minta izin lokasi (Geolocation API)
   ↓
3. Jika izin diberikan → Ambil lat/lon user
   ↓
4. Hitung jarak user dengan kantor (Haversine formula)
   ↓
5. Update display real-time
   ├─ Jika jarak ≤ 30m → Badge HIJAU + Button AKTIF
   └─ Jika jarak > 30m → Badge MERAH + Button DISABLED
   ↓
6. User klik tombol absen
   ↓
7. AJAX submit lat/lon ke backend
```

### Backend Flow:

```
1. Terima POST request dari frontend dengan lat/lon
   ↓
2. Validasi input (numeric, dalam range)
   ↓
3. Hitung jarak menggunakan Haversine (backend)
   ↓
4. Cek apakah dalam radius (backend validation)
   ↓
5. Jika valid:
   ├─ Simpan record + data lokasi ke DB
   └─ Return success JSON
   ↓
6. Jika invalid:
   └─ Reject request + return error JSON
```

### Data yang Tersimpan:

```sql
INSERT INTO attendances (
    employee_id,
    attendance_date,
    check_in,
    check_in_latitude,
    check_in_longitude,
    check_in_distance,
    check_out,
    check_out_latitude,
    check_out_longitude,
    check_out_distance,
    status
) VALUES (...)
```

---

## 🔒 KEAMANAN

✅ **Frontend Validation:**

- Geolocation API dengan high accuracy
- Real-time distance calculation
- Visual feedback

✅ **Backend Validation (PENTING):**

- Validasi ulang jarak menggunakan formula yang sama
- Tidak bisa dimanipulasi via browser console
- Data terenkripsi dengan Laravel middleware

✅ **Database:**

- Semua data lokasi tersimpan untuk audit trail
- Bisa tracking pergerakan karyawan

---

## 📱 BROWSER COMPATIBILITY

| Browser | Support | Geolocation |
| ------- | ------- | ----------- |
| Chrome  | ✅      | ✅          |
| Firefox | ✅      | ✅          |
| Safari  | ✅      | ✅          |
| Edge    | ✅      | ✅          |
| Opera   | ✅      | ✅          |

**Mobile:** Pastikan GPS enabled di device

---

## ⚙️ CONFIGURATION OPTIONS

### 1. Mengubah Radius

Edit `.env`:

```env
OFFICE_RADIUS=50  # Ubah dari 30 menjadi 50 meter
```

### 2. Mengubah Lokasi Kantor

Edit `.env`:

```env
OFFICE_LATITUDE=-6.123456
OFFICE_LONGITUDE=106.654321
```

### 3. Custom Check-in/Check-out Time

Edit `config/attendance.php`:

```php
'check_in_time' => env('CHECK_IN_TIME', '08:00:00'),
'check_out_time' => env('CHECK_OUT_TIME', '17:00:00'),
```

---

## 🛠️ TROUBLESHOOTING

### Geolocation tidak terdeteksi?

- ✅ Pastikan browser memberikan izin akses lokasi
- ✅ Aktifkan GPS di device
- ✅ Gunakan HTTPS (tidak bisa di HTTP lokal kecuali localhost)
- ✅ Refresh halaman

### Browser tidak support Geolocation?

- ✅ Gunakan browser modern (Chrome, Firefox, Safari, Edge)
- ✅ Update browser ke versi terbaru

### Jarak tidak akurat?

- ✅ Pastikan GPS signal kuat
- ✅ Tunggu beberapa detik untuk fix GPS
- ✅ Verifikasi koordinat kantor di Google Maps

### Button tidak aktif meski dalam area?

- ✅ Tunggu geolocation API selesai loading
- ✅ Refresh halaman
- ✅ Check console browser (F12 → Console)

---

## 📊 MONITORING

### Cek Data Absensi via PHP Artisan Tinker:

```php
php artisan tinker

# Query data dengan lokasi
>>> \App\Models\Attendance::with('employee')->latest()->take(10)->get();

# Hitung rata-rata jarak check-in
>>> \App\Models\Attendance::avg('check_in_distance');

# Employee yang sering absen dari luar kantor
>>> \App\Models\Attendance::where('check_in_distance', '>', 30)->count();
```

---

## 🎯 NEXT IMPROVEMENTS

Fitur-fitur yang bisa ditambahkan:

- [ ] Notifikasi real-time ke admin jika ada absensi dari luar
- [ ] Map visualization lokasi user saat absensi
- [ ] Photo capture saat absensi
- [ ] Approval workflow untuk absensi dari luar radius
- [ ] Analytics dashboard lokasi

---

## 📞 SUPPORT

Jika ada pertanyaan atau issue:

1. Cek console browser (F12)
2. Cek laravel logs: `storage/logs/laravel.log`
3. Jalankan `php artisan config:cache` untuk clear cache
4. Cek `.env` sudah dikonfigurasi dengan benar

---

**Created:** May 8, 2026  
**Version:** 1.0
