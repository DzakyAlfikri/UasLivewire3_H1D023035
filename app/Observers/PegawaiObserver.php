<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PegawaiObserver
{
    /**
     * Handle the Pegawai "created" event.
     */
    public function created(Pegawai $pegawai): void
    {
        // Skip if NIP is not set or empty
        if (empty($pegawai->nip)) {
            return;
        }
            
        // If employee already has a user account
        if ($pegawai->user_id) {
            return;
        }
            
        // Check if a user with this NIP already exists
        $existingUser = User::where('username', $pegawai->nip)->first();
        if ($existingUser) {
            // Link the employee to the existing user
            $pegawai->user_id = $existingUser->id;
            $pegawai->saveQuietly(); // Use saveQuietly to prevent infinite loop
            return;
        }
            
        // Create a new user account
        $user = User::create([
            'name' => $pegawai->nama,
            'email' => $pegawai->email ?? $pegawai->nip . '@example.com',
            'username' => $pegawai->nip,
            'password' => Hash::make('12345678'),
            'role' => 'pegawai'
        ]);
            
        // Link the employee to the user
        $pegawai->user_id = $user->id;
        $pegawai->saveQuietly(); // Use saveQuietly to prevent infinite loop
    }
}