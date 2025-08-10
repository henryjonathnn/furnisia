<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search functionality
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role - default to 'user' role if no filter specified
        $roleFilter = $request->has('role_filter') ? $request->role_filter : '3'; // 3 = user role
        if ($roleFilter !== '' && $roleFilter !== null) {
            $query->where('role_id', $roleFilter);
        }

        // Filter by status - default to active users if no filter specified
        $statusFilter = $request->has('status_filter') ? $request->status_filter : '1';
        
        // Only apply status filter if it's not empty string (empty = show all)
        if ($statusFilter !== '' && $statusFilter !== null) {
            $query->where('is_active', $statusFilter);
        }

        $users = $query->orderBy('is_active', 'desc') // Aktif users first
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $request->search,
                'role_filter' => $roleFilter,
                'status_filter' => $statusFilter
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        
        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');
        
        return Inertia::render('Admin/Users/Show', [
            'user' => $user
        ]);
    }

    /**
     * Get user details with statistics for modal view
     */
    public function getUserDetails(User $user)
    {
        try {
            // Load user with all relationships
            $user->load(['role', 'orders', 'carts', 'payments']);
            
            // Calculate statistics
            $userStats = [
                'total_orders' => $user->orders()->count(),
                'completed_orders' => $user->orders()->where('status', 'completed')->count(),
                'pending_orders' => $user->orders()->where('status', 'pending')->count(),
                'cancelled_orders' => $user->orders()->where('status', 'cancelled')->count(),
                'total_spent' => $user->orders()->where('status', 'completed')->sum('total'),
                'cart_items' => $user->carts()->count(),
                'last_activity' => $user->updated_at,
                'account_age_days' => $user->created_at->diffInDays(now()),
                'account_age_formatted' => $user->created_at->diffForHumans(),
            ];
            
            
            return response()->json([
                'user' => $user,
                'stats' => $userStats
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting user details: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to load user details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean'
        ]);

        // Only update password if provided
        if ($request->password) {
            $request->validate([
                'password' => 'string|min:8|confirmed'
            ]);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
