<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'online') {
                $query->where('is_online', true)
                    ->where('last_activity_at', '>=', now()->subMinutes(5));
            }
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'online_users' => User::where('is_online', true)
                ->where('last_activity_at', '>=', now()->subMinutes(5))
                ->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'hrd_users' => User::where('role', 'hrd')->count(),
            'employee_users' => User::where('role', 'employee')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,hrd,employee',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        $user = User::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_user',
            'description' => "User menambahkan akun baru: {$user->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['activityLogs' => function ($query) {
            $query->latest()->take(20);
        }]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,hrd,employee',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $oldData = $user->only(['name', 'email', 'role', 'is_active']);

        $validated['is_active'] = $request->has('is_active');

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Log activity
        $changes = [];
        foreach ($validated as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[] = ucfirst($key) . ": {$oldData[$key]} → {$value}";
            }
        }

        if (!empty($changes)) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'update_user',
                'description' => 'User mengubah data akun: ' . implode(', ', $changes),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Reset password user oleh admin
     */
    public function resetPassword(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Anda tidak memiliki izin untuk mereset password.');
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak dapat mereset password akun sendiri.');
        }

        $defaultPassword = config('auth.default_reset_password', 'Simsdm1234!');

        $user->update([
            'password' => Hash::make($defaultPassword),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'reset_password',
            'description' => 'Admin mereset password user ' . $user->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Password user ' . $user->name . ' berhasil direset menjadi: ' . $defaultPassword);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri.'
            ], 422);
        }

        $userName = $user->name;

        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'delete_user',
            'description' => "User menghapus akun: {$userName}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    }

    /**
     * Show user monitoring page
     */
    public function monitoring(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'online') {
                $query->where('is_online', true)
                    ->where('last_activity_at', '>=', now()->subMinutes(5));
            } elseif ($request->status === 'offline') {
                $query->where(function ($q) {
                    $q->where('is_online', false)
                        ->orWhere('last_activity_at', '<', now()->subMinutes(5));
                });
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        $roles = [
            'admin' => 'Admin',
            'hrd' => 'HRD',
            'employee' => 'Employee',
        ];

        return view('admin.users.monitoring', compact('users', 'roles'));
    }

    /**
     * Show user activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('activity', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $request->search . '%')
                    ->orWhere('user_agent', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('activity', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activityLogs = $query->orderBy('created_at', 'desc')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $users = User::select('id', 'name', 'email')->get();
        $actions = ActivityLog::distinct()->pluck('activity');

        return view('admin.users.activity-logs', compact('activityLogs', 'users', 'actions'));
    }
}
