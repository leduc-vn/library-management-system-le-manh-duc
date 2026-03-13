<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! method_exists($user, 'isLibrarian') || ! $user->isLibrarian()) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Only show users that have the 'reader' role
        $users = User::with('roles')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'reader');
            })
            ->orderBy('name')
            ->paginate(10);
        return view('librarian.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('librarian.users.show', compact('user'));
    }
}
