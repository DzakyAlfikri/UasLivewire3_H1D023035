<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountsFromPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pegawai:create-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user accounts for all employees with NIP as username and default password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if user_id column exists
        if (!Schema::hasColumn('pegawai', 'user_id')) {
            $this->error('The user_id column does not exist in the pegawai table.');
            $this->info('Please run the migration to add this column first.');
            return 1;
        }

        // Instead of filtering by user_id, get all employees and create accounts as needed
        $pegawais = Pegawai::all();
        $created = 0;
        $linked = 0;
        $skipped = 0;

        $this->info('Processing ' . count($pegawais) . ' employees...');
        
        foreach ($pegawais as $pegawai) {
            // Skip if NIP is not set or empty
            if (empty($pegawai->nip)) {
                $this->warn("Skipping employee ID {$pegawai->id}: No NIP found");
                $skipped++;
                continue;
            }
            
            // If employee already has a user account
            if ($pegawai->user_id) {
                $this->line("Employee {$pegawai->nama} (NIP: {$pegawai->nip}) already has a user account");
                $skipped++;
                continue;
            }
            
            // Check if a user with this NIP already exists
            $existingUser = User::where('username', $pegawai->nip)->first();
            if ($existingUser) {
                // Link the employee to the existing user
                $pegawai->user_id = $existingUser->id;
                $pegawai->save();
                $this->info("Linked employee {$pegawai->nama} to existing user account with NIP {$pegawai->nip}");
                $linked++;
                continue;
            }
            
            // Create a new user account
            $user = User::create([
                'name' => $pegawai->nama,
                'email' => $pegawai->email ?? $pegawai->nip . '@example.com', // Use email if available, otherwise generate one
                'username' => $pegawai->nip,
                'password' => Hash::make('12345678'),
                'role' => 'pegawai'
            ]);
            
            // Link the employee to the user
            $pegawai->user_id = $user->id;
            $pegawai->save();
            
            $this->info("Created user account for {$pegawai->nama} with NIP {$pegawai->nip}");
            $created++;
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->info("- Created {$created} new user accounts");
        $this->info("- Linked {$linked} employees to existing accounts");
        $this->info("- Skipped {$skipped} employees");
        
        return 0;
    }
}
