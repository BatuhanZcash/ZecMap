<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role');

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when(
                in_array($role, [User::ROLE_ADMIN, User::ROLE_USER], true),
                fn ($query) => $query->where('role', $role),
            )
            ->orderByDesc('created_at')
            ->get();

        $totalUsers = User::count();
        $adminCount = User::where('role', User::ROLE_ADMIN)->count();

        return view('Admin.users', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'totalUsers' => $totalUsers,
            'adminCount' => $adminCount,
        ]);
    }
}
