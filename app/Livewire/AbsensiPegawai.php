<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Absensi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiPegawai extends Component
{
    public $pegawai;
    public $todayAttendance;
    public $keterangan;
    public $bulan;
    public $tahun;
    public $attendanceRecords = [];
    public $stats = [];
    public $showMessage = false;
    public $message = [];
    
    public function mount()
    {
        // Get current user's employee data
        $this->pegawai = Auth::user()->pegawai;
        
        // Set default month and year to current
        $this->bulan = Carbon::now()->month;
        $this->tahun = Carbon::now()->year;
        
        $this->loadData();
    }
    
    public function loadData()
    {
        if (!$this->pegawai) {
            return;
        }
        
        // Get today's attendance record
        $this->todayAttendance = Absensi::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();
            
        // Get attendance records for selected month/year
        $this->loadAttendanceRecords();
        
        // Calculate attendance statistics
        $this->calculateStats();
    }
    
    public function loadAttendanceRecords()
    {
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();
        
        $records = Absensi::where('pegawai_id', $this->pegawai->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Transform into a date-indexed array
        $this->attendanceRecords = [];
        foreach ($records as $record) {
            $date = Carbon::parse($record->tanggal)->format('Y-m-d');
            $this->attendanceRecords[$date] = $record;
        }
    }
    
    public function calculateStats()
    {
        // Reset stats
        $this->stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'total' => 0,
            'persentase' => 0
        ];
        
        // Get working days in the month (excluding weekends)
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();
        
        $workingDays = 0;
        for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
            if (!$day->isWeekend() && $day->lte(Carbon::today())) {
                $workingDays++;
            }
        }
        
        // Count by status
        foreach ($this->attendanceRecords as $record) {
            if (is_object($record) && property_exists($record, 'status')) {
                if ($record->status == 'hadir') {
                    $this->stats['hadir']++;
                } elseif ($record->status == 'sakit') {
                    $this->stats['sakit']++;
                } elseif ($record->status == 'izin') {
                    $this->stats['izin']++;
                } else {
                    $this->stats['alpha']++;
                }
            }
        }
        
        // Calculate alphas (absences without permission)
        // Alpha = working days - (present + sick + permission)
        $recordedDays = $this->stats['hadir'] + $this->stats['sakit'] + $this->stats['izin'];
        $this->stats['alpha'] = max(0, $workingDays - $recordedDays);
        
        // Total days and percentage
        $this->stats['total'] = $workingDays;
        $this->stats['persentase'] = $workingDays > 0 
            ? round(($this->stats['hadir'] / $workingDays) * 100) 
            : 0;
    }
    
    public function submitAttendance($status)
    {
        if (!$this->pegawai) {
            $this->showMessage('Data pegawai tidak ditemukan', 'error');
            return;
        }
        
        // Check if already submitted today
        if ($this->todayAttendance) {
            // Update existing record
            $this->todayAttendance->status = $status;
            if (!empty($this->keterangan)) {
                $this->todayAttendance->keterangan = $this->keterangan;
            }
            $this->todayAttendance->save();
            
            $this->showMessage('Absensi berhasil diperbarui', 'success');
        } else {
            // Create new record
            $attendance = new Absensi();
            $attendance->pegawai_id = $this->pegawai->id;
            $attendance->tanggal = Carbon::now();
            $attendance->status = $status;
            $attendance->keterangan = $this->keterangan;
            $attendance->save();
            
            $this->showMessage('Absensi berhasil disimpan', 'success');
        }
        
        // Reset form and reload data
        $this->reset('keterangan');
        $this->loadData();
    }
    
    public function changeMonth($direction)
    {
        $date = Carbon::createFromDate($this->tahun, $this->bulan, 1);
        
        if ($direction === 'prev') {
            $date->subMonth();
        } else {
            $date->addMonth();
        }
        
        $this->bulan = $date->month;
        $this->tahun = $date->year;
        
        $this->loadAttendanceRecords();
        $this->calculateStats();
    }
    
    private function showMessage($text, $type = 'success')
    {
        $this->message = [
            'text' => $text,
            'type' => $type
        ];
        $this->showMessage = true;
        
        // Auto-hide message after 3 seconds
        $this->dispatch('hideMessage');
    }
    
    public function render()
    {
        return view('livewire.absensi-pegawai');
    }
}
