<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role != 'superadmin') {
            abort(403, 'Tidak Memiliki Akses!');
        }

        if ($request->has('search') && $request->search !== null) {
            $search = strtolower($request->search);
            $users = User::whereRaw('LOWER(name) LIKE ?', ['%'.$search.'%'])
                ->paginate(10)
                ->appends($request->only('search'));
        } else {
            $users = User::paginate(10);
        }

        return view('superadmin.user.index', compact('users'));
    }

    public function create()
    {
        return view('superadmin.user.create');
    }

    public function store(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('message', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::find($id);

        return view('superadmin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('message', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('user.index');
    }
}
