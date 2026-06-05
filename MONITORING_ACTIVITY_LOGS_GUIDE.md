# Panduan Monitoring User & Activity Logs

## 📋 Daftar Isi

1. [Ringkasan Fitur](#ringkasan-fitur)
2. [Akses Menu](#akses-menu)
3. [Halaman Monitoring User](#halaman-monitoring-user)
4. [Halaman Activity Logs](#halaman-activity-logs)
5. [Cara Mencatat Aktivitas](#cara-mencatat-aktivitas)
6. [Database Structure](#database-structure)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 Ringkasan Fitur

Fitur monitoring user dan activity logs memungkinkan admin untuk:

- **Monitoring User**: Melihat status online/offline user, last login, IP address, device info
- **Activity Logs**: Mencatat dan melihat semua aktivitas penting di sistem (login, logout, CRUD pegawai, absensi, dll)

---

## 🔗 Akses Menu

### Di Sidebar Admin:

```
Manajemen User
├── Data User           → /admin/users
├── Monitoring User     → /admin/users/monitoring
└── Activity Logs       → /admin/users/activity-logs
```

### Route Names:

```php
route('admin.users.index')         // Data User
route('admin.users.monitoring')    // Monitoring User
route('admin.users.activity-logs') // Activity Logs
```

---

## 👁️ Halaman Monitoring User

### Fitur Filter:

```
┌─────────────────────────────────────────────┐
│ Cari User         │ Role  │ Status │ Filter │
├─────────────────────────────────────────────┤
│ • Search name/email                         │
│ • Filter role: Admin, HRD, Employee         │
│ • Filter status: Online, Offline, Active    │
│ • Pagination: 15 user per halaman           │
└─────────────────────────────────────────────┘
```

### Data Ditampilkan:

| Kolom              | Keterangan                     |
| ------------------ | ------------------------------ |
| Nama               | Nama user                      |
| Email              | Email user                     |
| Role               | Admin/HRD/Employee             |
| Status             | Online/Offline/Active/Inactive |
| Login Terakhir     | Tanggal dan jam login terakhir |
| IP Terakhir        | IP address saat login terakhir |
| User Agent         | Browser/device info            |
| Aktivitas Terakhir | Kapan user terakhir kali aktif |

### Badge Status:

- 🟢 **Online**: User online dalam 5 menit terakhir dan is_online = true
- 🔴 **Offline**: User tidak aktif lebih dari 5 menit
- ⚫ **Nonaktif**: User tidak aktif (is_active = false)

---

## 📊 Halaman Activity Logs

### Fitur Filter & Search:

```
┌──────────────────────────────────────────────────────┐
│ Cari │ User │ Aktivitas │ Dari Tgl │ Sampai Tgl │ 🔍 │
├──────────────────────────────────────────────────────┤
│ • Search: user, aktivitas, ip, deskripsi            │
│ • Filter by user: pilih user spesifik               │
│ • Filter by activity: login, logout, check_in, dll  │
│ • Filter by date range: dari tanggal ke tanggal     │
│ • Pagination: 25 logs per halaman                   │
└──────────────────────────────────────────────────────┘
```

### Data Ditampilkan:

| Kolom      | Keterangan                                   |
| ---------- | -------------------------------------------- |
| Waktu      | Tanggal dan jam aktivitas (formatted)        |
| User       | Nama dan email user yang melakukan aktivitas |
| Aktivitas  | Jenis aktivitas (badge color-coded)          |
| Deskripsi  | Detail aktivitas + metadata                  |
| IP Address | IP address saat melakukan aktivitas          |

### Jenis Aktivitas & Badge Color:

- 🟢 **login**: User berhasil login
- ⚫ **logout**: User logout dari sistem
- 🔵 **create_employee**: Tambah data pegawai baru
- 🔷 **update_employee**: Ubah data pegawai
- 🔴 **delete_employee**: Hapus data pegawai
- 🟢 **check_in**: Absensi masuk
- 🟡 **check_out**: Absensi pulang
- 🔵 **create_user**: Tambah user baru
- 🔷 **update_user**: Ubah data user
- 🔴 **delete_user**: Hapus user

---

## 📝 Cara Mencatat Aktivitas

### 1️⃣ Basic Usage

```php
// Di dalam controller yang sudah authenticated
auth()->user()->logActivity('login', 'User logged in to the system');
```

### 2️⃣ Dengan Metadata (Additional Data)

```php
auth()->user()->logActivity(
    'check_in',
    'Checked in attendance for John Doe',
    [
        'distance' => 50.5,
        'latitude' => -6.2088,
        'longitude' => 106.8456,
    ]
);
```

### 3️⃣ User Model Method (Sudah Ada)

```php
// Method di User model
$user->updateLastLogin();           // Saat login
$user->updateLastActivity();        // Update saat user aktif
$user->markAsOffline();             // Saat logout
$user->logActivity($action, $desc); // Catat aktivitas
```

---

## 📚 Contoh Implementasi di Controller

### Login (AuthenticatedSessionController.php)

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // ✅ Otomatis mencatat login metadata
    $request->user()->updateLastLogin();

    $role = $request->user()->role;
    return match($role) {
        'admin'    => redirect()->route('admin.dashboard'),
        'hrd'      => redirect()->route('hrd.dashboard'),
        'employee' => redirect()->route('employee.dashboard'),
        default    => $this->logoutAndRedirect(),
    };
}

public function destroy(Request $request): RedirectResponse
{
    $user = Auth::user();
    if ($user) {
        // ✅ Otomatis mencatat logout
        $user->markAsOffline();
    }

    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

### Employee CRUD (EmployeeController.php)

```php
public function store(Request $request)
{
    // ... validation & create employee ...

    // ✅ Catat aktivitas pembuatan pegawai
    auth()->user()->logActivity(
        'create_employee',
        "Created employee: {$request->full_name}"
    );

    return redirect()->route(auth()->user()->role . '.karyawan.index')
                    ->with('success', 'Pegawai berhasil ditambahkan');
}

public function update(Request $request, Employee $karyawan)
{
    // ... update employee ...

    // ✅ Catat aktivitas update pegawai
    auth()->user()->logActivity(
        'update_employee',
        "Updated employee: {$karyawan->full_name}"
    );

    return redirect()->route(auth()->user()->role . '.karyawan.index')
                    ->with('success', 'Data pegawai diperbarui');
}

public function destroy(Employee $karyawan)
{
    // ✅ Catat sebelum hapus
    auth()->user()->logActivity(
        'delete_employee',
        "Deleted employee: {$karyawan->full_name}"
    );

    $karyawan->delete();

    return redirect()->route(auth()->user()->role . '.karyawan.index')
                    ->with('success', 'Pegawai dihapus');
}
```

### Attendance (EmployeeDashboardController.php)

```php
public function storeAbsensi(Request $request)
{
    // ... validation & create attendance ...

    // ✅ Catat aktivitas check-in dengan detail lokasi
    $user->logActivity(
        'check_in',
        "Checked in attendance for {$employee->full_name}",
        [
            'distance' => round($distance, 2),
            'latitude' => $userLat,
            'longitude' => $userLon,
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Berhasil absen masuk!'
    ]);
}

public function updateAbsensi(Request $request)
{
    // ... validation & update attendance checkout ...

    // ✅ Catat aktivitas check-out dengan detail lokasi
    $user->logActivity(
        'check_out',
        "Checked out attendance for {$employee->full_name}",
        [
            'distance' => round($distance, 2),
            'latitude' => $userLat,
            'longitude' => $userLon,
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Berhasil absen pulang!'
    ]);
}
```

---

## 🗄️ Database Structure

### Tabel: activity_logs

```sql
CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULLABLE,
    activity VARCHAR(255),
    description TEXT NULLABLE,
    metadata JSON NULLABLE,
    ip_address VARCHAR(255) NULLABLE,
    user_agent TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id, created_at),
    INDEX (activity)
);
```

### Kolom users (Updated):

```sql
ALTER TABLE users ADD COLUMN (
    last_login_at TIMESTAMP NULLABLE,
    last_login_ip VARCHAR(255) NULLABLE,
    last_login_user_agent TEXT NULLABLE,
    is_online BOOLEAN DEFAULT FALSE,
    last_activity_at TIMESTAMP NULLABLE
);
```

### Migration Files:

- `2026_05_08_000001_add_user_management_fields_to_users_table.php` - Tambah kolom monitoring ke users
- `2026_05_08_000002_create_user_activity_logs_table.php` - Tabel user_activity_logs (lama)
- `2026_05_08_200733_create_activity_logs_table.php` - Tabel activity_logs (baru)

---

## 🛠️ Model Classes

### ActivityLog Model

```php
namespace App\Models;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $fillable = ['user_id', 'activity', 'description', 'metadata', 'ip_address', 'user_agent'];
    protected $casts = ['metadata' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function getActionBadgeClass() { ... }
    public function getActivityName() { ... }
}
```

### User Model Methods

```php
class User extends Authenticatable
{
    // Relationships
    public function activityLogs() { return $this->hasMany(ActivityLog::class)->latest(); }

    // Activity tracking
    public function updateLastLogin($ip = null, $userAgent = null) { ... }
    public function updateLastActivity() { ... }
    public function markAsOffline() { ... }
    public function logActivity($action, $description, $metadata = null) { ... }
    public function isCurrentlyOnline() { ... }
}
```

---

## 🐛 Troubleshooting

### 1. Route 404 Not Found

**Masalah**: `/admin/users/monitoring` atau `/admin/users/activity-logs` menampilkan 404

**Solusi**:

```bash
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear
```

### 2. UserController Not Found

**Masalah**: Target class UserController does not exist

**Solusi**:

```bash
composer dump-autoload
php artisan route:clear
```

### 3. ActivityLog Model Not Found

**Masalah**: Class App\Models\ActivityLog not found

**Solusi**:

```bash
composer dump-autoload
php artisan optimize
```

### 4. Kolom activity_logs tidak ada

**Masalah**: SQLSTATE[42S22]: Unknown column 'created_at'

**Solusi**:

```bash
php artisan migrate
php artisan migrate --refresh  # Jika ada masalah
```

### 5. Activity Log tidak tersimpan

**Masalah**: Aktivitas tidak tercatat di tabel

**Solusi**:

- Pastikan user sudah authenticated: `auth()->check()`
- Gunakan `auth()->user()->logActivity()` atau `$user->logActivity()`
- Jangan lupa call di method yang tepat (setelah create/update/delete)

---

## ✅ Checklist Implementasi

- ✅ Migration `activity_logs` dibuat dan dijalankan
- ✅ Model `ActivityLog` dibuat dengan relationships dan helpers
- ✅ User model diupdate dengan activity tracking methods
- ✅ Routes `/admin/users/monitoring` dan `/admin/users/activity-logs` aktif
- ✅ Controller methods `monitoring()` dan `activityLogs()` di UserController
- ✅ Blade views `monitoring.blade.php` dan `activity-logs.blade.php` dibuat
- ✅ Login/logout tracking di AuthenticatedSessionController
- ✅ Employee CRUD logging di EmployeeController
- ✅ Attendance check-in/out logging di EmployeeDashboardController
- ✅ Sidebar links sudah update
- ✅ Database schema compatible dengan MySQL

---

## 📖 Catatan Penting

1. **Activity Log tergantung pada User**: Jika user dihapus, semua activity logs-nya juga terhapus (CASCADE delete)

2. **Online Status**: User dianggap online jika `is_online = true` DAN `last_activity_at >= now() - 5 minutes`

3. **IP Address & User Agent**: Otomatis tercatat dari `request()->ip()` dan `request()->userAgent()`

4. **Metadata**: Gunakan array untuk menyimpan data tambahan yang akan di-serialize ke JSON

5. **Search & Filter**: Semua halaman mendukung search dan filter untuk kemudahan monitoring

---

## 📞 Support

Jika ada masalah atau pertanyaan:

1. Cek terminal output untuk error messages
2. Gunakan `php artisan tinker` untuk test model relationships
3. Cek file logs di `storage/logs/` untuk detail error
4. Jalankan `php artisan migrate:status` untuk verifikasi migrations

---

**Last Updated**: May 8, 2026
**Version**: 1.0
