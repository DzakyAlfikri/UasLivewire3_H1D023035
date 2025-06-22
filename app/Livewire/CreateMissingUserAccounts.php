<?php

namespace App\Livewire;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CreateMissingUserAccounts extends Component
{
    public $message = '';
    public $status = '';
    
    public function createMissingAccounts()
    {
        $pegawais = Pegawai::whereNull('user_id')->get();
        $created = 0;
        $linked = 0;
        $skipped = 0;
        
        foreach ($pegawais as $pegawai) {
            // Skip if NIP is not set or empty
            if (empty($pegawai->nip)) {
                $skipped++;
                continue;
            }
            
            // Check if a user with this NIP already exists
            $existingUser = User::where('username', $pegawai->nip)->first();
            if ($existingUser) {
                // Link the employee to the existing user
                $pegawai->user_id = $existingUser->id;
                $pegawai->save();
                $linked++;
                continue;
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
            $pegawai->save();
            $created++;
        }
        
        $this->message = "Created $created new accounts, linked $linked existing accounts, skipped $skipped employees.";
        $this->status = 'success';
    }
    
    public function render()
    {
        $missingAccounts = Pegawai::whereNull('user_id')->count();
        return view('livewire.create-missing-user-accounts', [
            'missingAccounts' => $missingAccounts
        ]);
    }
}
