<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PegawaiDashboard extends Component
{
    public $pegawai;
    public $todayAttendance;
    public $recentAttendance;
    public $cutiList;
    public $salarySlips;
    
    // For attendance submission
    public $tanggal;
    public $status;
    public $message;
    public $showMessage = false;
    
    public function mount()
    {
        // Get current authenticated user's employee data
        $this->pegawai = Auth::user()->pegawai;
        $this->tanggal = Carbon::now()->format('Y-m-d');
        
        $this->loadData();
    }
    
    public function loadData()
    {
        if ($this->pegawai) {
            // Check if attendance already exists for today
            $this->todayAttendance = Absensi::where('pegawai_id', $this->pegawai->id)
                ->whereDate('tanggal', Carbon::today())
                ->first();
                
            // Get recent attendance history
            $this->recentAttendance = Absensi::where('pegawai_id', $this->pegawai->id)
                ->latest()
                ->take(5)
                ->get();
                
            // Get recent leave requests
            $this->cutiList = Cuti::where('pegawai_id', $this->pegawai->id)
                ->latest()
                ->take(3)
                ->get();
                
            // Example salary slip data (replace with actual data if available)
            $this->salarySlips = collect([
                (object)['period' => 'Juni 2025', 'amount' => 5000000, 'date' => '30 Juni 2025'],
                (object)['period' => 'Mei 2025', 'amount' => 5000000, 'date' => '31 Mei 2025'],
                (object)['period' => 'April 2025', 'amount' => 4800000, 'date' => '30 April 2025'],
            ]);
        }
    }
    
    public function submitAttendance($attendanceStatus)
    {
        if (!$this->pegawai) {
            $this->showMessage('Data pegawai tidak ditemukan', 'error');
            return;
        }
        
        // Check if attendance already exists
        if ($this->todayAttendance) {
            // Update existing attendance
            $this->todayAttendance->status = $attendanceStatus;
            $this->todayAttendance->save();
            
            $this->showMessage('Absensi berhasil diperbarui', 'success');
        } else {
            // Create new attendance
            Absensi::create([
                'pegawai_id' => $this->pegawai->id,
                'tanggal' => Carbon::today(),
                'status' => $attendanceStatus
            ]);
            
            $this->showMessage('Absensi berhasil disimpan', 'success');
        }
        
        // Refresh data
        $this->loadData();
    }
    
    private function showMessage($message, $type = 'success')
    {
        $this->message = [
            'text' => $message,
            'type' => $type
        ];
        $this->showMessage = true;
        
        // Auto-hide message after 3 seconds
        $this->dispatch('hideMessage');
    }
    
    public function render()
    {
        return view('livewire.pegawai-dashboard');
    }
}
