<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cuti;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CutiRequest extends Component
{
    public $tanggal_mulai;
    public $tanggal_akhir;
    public $keterangan;
    public $durasi = 0;
    public $sisaCuti = 0;
    public $pegawai;
    
    public function mount()
    {
        // Get the currently logged in user's employee data
        $this->pegawai = Auth::user()->pegawai;
        
        // Set default dates (today and tomorrow)
        $this->tanggal_mulai = Carbon::now()->format('Y-m-d');
        $this->tanggal_akhir = Carbon::now()->addDay()->format('Y-m-d');
        
        // Update the leave days calculation
        $this->updateDurasi();
        $this->updateSisaCuti();
    }
    
    public function updatedTanggalMulai()
    {
        $this->updateDurasi();
        $this->updateSisaCuti();
    }
    
    public function updatedTanggalAkhir()
    {
        $this->updateDurasi();
    }
    
    public function updateDurasi()
    {
        if (empty($this->tanggal_mulai) || empty($this->tanggal_akhir)) {
            $this->durasi = 0;
            return;
        }
        
        $startDate = Carbon::parse($this->tanggal_mulai);
        $endDate = Carbon::parse($this->tanggal_akhir);
        
        // Ensure end date is not before start date
        if ($endDate->lt($startDate)) {
            $this->tanggal_akhir = $this->tanggal_mulai;
            $endDate = $startDate;
        }
        
        // Calculate working days (excluding weekends)
        $this->durasi = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $this->durasi++;
            }
        }
    }
    
    protected function updateSisaCuti()
    {
        if ($this->pegawai) {
            // Determine which year to check (from selected start date or current year)
            $year = !empty($this->tanggal_mulai) 
                ? Carbon::parse($this->tanggal_mulai)->year 
                : Carbon::now()->year;
            
            // Get all approved and pending leave requests for this employee in the specified year
            $cutiTerpakai = $this->pegawai->getCutiTerpakai($year);
            
            // Calculate remaining leave days (assuming 12 days per year)
            $this->sisaCuti = 12 - $cutiTerpakai;
        } else {
            $this->sisaCuti = 0;
        }
    }
    
    public function save()
    {
        $this->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|min:5',
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_akhir.required' => 'Tanggal akhir harus diisi',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.min' => 'Keterangan minimal 5 karakter',
        ]);
        
        // Check if employee has enough leave days
        if ($this->durasi > $this->sisaCuti) {
            session()->flash('error', 'Jumlah hari cuti melebihi sisa cuti yang tersedia');
            return;
        }
        
        // Create the leave request
        Cuti::create([
            'pegawai_id' => $this->pegawai->id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_akhir' => $this->tanggal_akhir,
            'durasi' => $this->durasi,
            'keterangan' => $this->keterangan,
            'status' => 'pending', // Default status is pending
        ]);
        
        session()->flash('message', 'Pengajuan cuti berhasil dibuat dan sedang menunggu persetujuan');
        
        // Reset form
        $this->reset(['keterangan']);
        $this->tanggal_mulai = Carbon::now()->format('Y-m-d');
        $this->tanggal_akhir = Carbon::now()->addDay()->format('Y-m-d');
        $this->updateDurasi();
        $this->updateSisaCuti();
    }
    
    public function render()
    {
        return view('livewire.cuti-request');
    }
}
