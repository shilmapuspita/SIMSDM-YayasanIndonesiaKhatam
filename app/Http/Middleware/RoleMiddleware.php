<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // PERHATIKAN: 'string $role' saya ganti jadi '...$roles' (Titik tiga)
    // Ini artinya: Tangkap SEMUA role yang dikirim (admin, hrd, dll) langsung jadi Array.
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 1. Ambil Role User (Bersihkan spasi & huruf kecil)
        $userRole = strtolower(trim($user->role));

        // 2. $roles SUDAH berupa Array ["admin", "hrd"]. 
        // Kita tidak perlu explode lagi. Cukup bersihkan isinya.
        $allowedRoles = array_map(function ($role) {
            return strtolower(trim($role));
        }, $roles); // Gunakan variabel $roles yang dari parameter di atas

        // 3. Cek apakah role user ada di dalam array
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // Kalau gagal, kita debug (Hapus dd ini nanti kalau sudah fix)
        // dd([
        //    'User Role' => $userRole,
        //    'Allowed' => $allowedRoles
        // ]);

        abort(403, 'Unauthorized access.');
    }
}