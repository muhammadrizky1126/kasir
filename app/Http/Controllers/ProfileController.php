<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Pastikan model User di-import

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.Auth::id(),
        ]);

        $user = User::find(Auth::id()); // Mengambil user dari model secara eksplisit
        $user->name = $request->name;
        $user->email = $request->email;

        try {
            $user->save();
            return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui profil: '.$e->getMessage());
        }
    }

    public function changepassword()
    {
        return view('profile.changepassword');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find(Auth::id()); // Mengambil user dari model secara eksplisit

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        $user->password = Hash::make($request->new_password);

        try {
            $user->save();
            return back()->with('success', 'Password berhasil diubah!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah password: '.$e->getMessage());
        }
    }
}
