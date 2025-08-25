<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $user->load('role', 'stockMovements.product');

        // Son stok hareketleri
        $recentMovements = $user->stockMovements()
            ->with(['product', 'customer'])
            ->latest()
            ->take(10)
            ->get();

        // İstatistikler
        $stats = [
            'total_movements' => $user->stockMovements()->count(),
            'total_entries' => $user->stockMovements()->where('type', 'giriş')->count(),
            'total_exits' => $user->stockMovements()->where('type', 'çıkış')->count(),
            'total_returns' => $user->stockMovements()->where('type', 'iade')->count(),
            'this_month_movements' => $user->stockMovements()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('profile.show', compact('user', 'recentMovements', 'stats'));
    }

    /**
     * Show the form for editing the user's profile.
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
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil bilgileriniz başarıyla güncellendi.');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword(): View
    {
        return view('profile.password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.password')->with('success', 'Şifreniz başarıyla değiştirildi.');
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

        // Admin kullanıcısı kendini silemez
        if ($user->id === 1) {
            return redirect()->route('profile.show')->with('error', 'Ana admin kullanıcısı silinemez.');
        }

        // Stok hareketleri varsa hesap silinemez
        if ($user->stockMovements()->count() > 0) {
            return redirect()->route('profile.show')->with('error', 'Stok hareketleriniz bulunduğu için hesabınız silinemez. Lütfen yöneticiyle iletişime geçin.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Hesabınız başarıyla silindi.');
    }
}
