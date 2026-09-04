<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Tampilkan form ganti password.
     */
    public function edit()
    {
        return view('auth.change-password');
    }

    /**
     * Proses update password.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 'different:current_password'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.different' => 'Password baru harus berbeda dengan password saat ini.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $backUrl = $request->user()->isAdmin() ? route('admin.dashboard') : route('dashboard');

        return redirect($backUrl)->with('success', 'Password berhasil diperbarui!');
    }
}
