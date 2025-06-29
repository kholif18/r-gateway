<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\WhatsappHelper;

class UserController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('user', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|confirmed|min:8',
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.file' => 'File tidak valid atau terlalu besar.',
            'avatar.mimes' => 'Format file tidak didukung. Gunakan jpeg, png, jpg, atau gif.',
            'avatar.max' => 'Ukuran file terlalu besar. Maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ✅ Update data user
        $user->fill([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => WhatsappHelper::normalizePhoneNumber($request->phone), // ⬅️ gunakan helper
            'address' => $request->address,
        ]);

        // ✅ Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // ✅ Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
