<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Pegawai;
use Livewire\Component;
use Carbon\Carbon;

class LaporanAbsensi extends Component
{
    public $bulan;
    public $tahun;
    public $pegawai_id;
    public $selectedPegawai = null;
    public $detailedAttendance = [];
    public $showDetailModal = false;

    public function mount()
    {
        $this->bulan = Carbon::now()->month;
        $this->tahun = Carbon::now()->year;
    }
    
    public function showAttendanceDetail($pegawaiId)
    {
        $this->selectedPegawai = Pegawai::with(['jabatan', 'unitKerja'])->find($pegawaiId);
        
        if (!$this->selectedPegawai) {
            return;
        }
        
        // Get start and end dates for the month
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();
        $today = Carbon::today();
        
        // Limit to today if viewing current month
        if ($endDate->gt($today) && $this->bulan == $today->month && $this->tahun == $today->year) {
            $endDate = $today;
        }
        
        // Get all attendance records for this employee in the month
        $attendanceRecords = Absensi::where('pegawai_id', $pegawaiId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });
        
        // Prepare detailed attendance data
        $this->detailedAttendance = [];
        
        // Loop through each day in the month
        for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
            $dateStr = $day->format('Y-m-d');
            $isWeekend = $day->isWeekend();
            $record = $attendanceRecords->get($dateStr);
            
            $status = 'alpha'; // Default status is absent
            
            if ($isWeekend) {
                $status = 'weekend';
            } elseif ($record) {
                $status = $record->status;
            }
            
            $this->detailedAttendance[] = [
                'date' => $dateStr,
                'day' => $day->format('l'),
                'formatted_date' => $day->format('d/m/Y'),
                'is_weekend' => $isWeekend,
                'status' => $status,
                'time' => $record ? Carbon::parse($record->created_at)->format('H:i:s') : null,
                'notes' => $record ? $record->keterangan : null
            ];
        }
        
        // Open the modal
        $this->showDetailModal = true;
    }
    
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedPegawai = null;
        $this->detailedAttendance = [];
    }

    public function render()
    {
        $query = Pegawai::with(['absensi' => function($query) {
            $query->whereMonth('tanggal', $this->bulan)
                  ->whereYear('tanggal', $this->tahun);
        }, 'jabatan', 'unitKerja']);

        if ($this->pegawai_id) {
            $query->where('id', $this->pegawai_id);
        }

        $pegawaiData = $query->get();
        
        // Get working days in the month (excluding weekends)
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();
        $today = Carbon::today();
        
        // Limit to today if we're viewing the current month
        if ($endDate->gt($today) && $this->bulan == $today->month && $this->tahun == $today->year) {
            $endDate = $today;
        }
        
        $workingDays = 0;
        for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
            if (!$day->isWeekend()) {
                $workingDays++;
            }
        }

        // Rekapitulasi per pegawai using the same logic as AbsensiPegawai
        $rekapitulasi = collect();
        
        foreach ($pegawaiData as $pegawai) {
            $absensiData = $pegawai->absensi->where('tanggal', '>=', $startDate)
                                            ->where('tanggal', '<=', $endDate);
            
            $hadir = $absensiData->where('status', 'hadir')->count();
            $sakit = $absensiData->where('status', 'sakit')->count();
            $izin = $absensiData->where('status', 'izin')->count();
            
            // Calculate alphas (absences without permission)
            // Alpha = working days - (present + sick + permission)
            $recordedDays = $hadir + $sakit + $izin;
            $alpha = max(0, $workingDays - $recordedDays);
            
            $rekapitulasi->push([
                'pegawai' => $pegawai,
                'hadir' => $hadir,
                'tidak_hadir' => $alpha, // Use alpha calculation here instead of tidak_hadir status
                'sakit' => $sakit,
                'izin' => $izin,
                'total' => $workingDays
            ]);
        }

        return view('livewire.laporan-absensi', [
            'rekapitulasi' => $rekapitulasi,
            'pegawais' => Pegawai::all(),
            'workingDays' => $workingDays,
            'bulanArray' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ]
        ]);
    }
}