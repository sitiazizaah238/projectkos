<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */

public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'password' => 'nullable|confirmed|min:6',
    ]);

    // ================= FOTO =================
    if ($request->hasFile('photo')) {

        // hapus lama
        if ($user->photo && Storage::exists('public/profile/' . $user->photo)) {
            Storage::delete('public/profile/' . $user->photo);
        }

        $file = $request->file('photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        $file->storeAs('profile', $filename, 'public');

        $user->photo = $filename;
    }

    // ================= DATA =================
    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return back()->with('success', 'Profile updated');
}
public function deletePhoto(Request $request)
{
    $user = Auth::user();

    // cek foto ada atau tidak
    if ($user->photo) {

        // hapus file dari storage
        if (Storage::exists('public/profile/' . $user->photo)) {
            Storage::delete('public/profile/' . $user->photo);
        }

        // hapus nama foto dari database
        $user->photo = null;
        $user->save();
    }

    return back()->with('success', 'Foto profil berhasil dihapus');
}
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
