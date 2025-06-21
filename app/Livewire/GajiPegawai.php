<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GajiPegawai extends Component
{
    public $pegawai;
    public $salaryDetail = [];
    public $selectedYear;
    public $years = [];
    public $months = [];
    public $selectedMonth;
    
    public function mount()
    {
        // Get current user's employee data
        $this->pegawai = Auth::user()->pegawai;
        
        // Set up available years (current year and 2 previous years)
        $currentYear = Carbon::now()->year;
        $this->years = [$currentYear, $currentYear - 1, $currentYear - 2];
        $this->selectedYear = $currentYear;
        
        // Set up months
        $this->months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $this->selectedMonth = Carbon::now()->month;
        
        $this->loadSalaryDetail();
    }
    
    public function loadSalaryDetail()
    {
        if (!$this->pegawai) {
            return;
        }
        
        // Get basic salary information
        $basicSalary = $this->pegawai->gaji;
        $allowance = $this->pegawai->jabatan->tunjangan ?? 0;
        $totalSalary = $this->pegawai->gaji_total;
        
        // Calculate attendance-based information (without deductions)
        $monthStart = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $monthEnd = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        // Count workdays in selected month
        $workdays = 0;
        for ($day = $monthStart->copy(); $day->lte($monthEnd); $day->addDay()) {
            if (!$day->isWeekend()) {
                $workdays++;
            }
        }
        
        // Get absences for the selected month
        $absences = $this->getAbsences($this->selectedYear, $this->selectedMonth);
        
        // No deductions - set everything to 0
        $deductionPerDay = 0;
        $totalDeduction = 0;
        
        // Final salary without deductions (equals total salary)
        $finalSalary = $totalSalary;
        
        // Prepare the salary detail array
        $this->salaryDetail = [
            'basic_salary' => $basicSalary,
            'allowance' => $allowance,
            'workdays' => $workdays,
            'absences' => $absences,
            'deduction_per_day' => $deductionPerDay,
            'total_deduction' => $totalDeduction,
            'total_salary' => $totalSalary,
            'final_salary' => $finalSalary,
            'payment_date' => $monthEnd->format('Y-m-d'),
            'period' => $this->months[$this->selectedMonth] . ' ' . $this->selectedYear,
        ];
    }
    
    private function getAbsences($year, $month)
    {
        if (!$this->pegawai) {
            return 0;
        }
        
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();
        
        // Count working days
        $workdays = 0;
        for ($day = $monthStart->copy(); $day->lte($monthEnd) && $day->lte(Carbon::today()); $day->addDay()) {
            if (!$day->isWeekend()) {
                $workdays++;
            }
        }
        
        // Count days with attendance
        $attendanceDays = $this->pegawai->absensi()
            ->whereIn('status', ['hadir', 'izin', 'sakit'])
            ->whereBetween('tanggal', [$monthStart, $monthEnd])
            ->count();
        
        // Absences = working days - attendance days
        return max(0, $workdays - $attendanceDays);
    }
    
    public function updatedSelectedYear()
    {
        $this->loadSalaryDetail();
    }
    
    public function updatedSelectedMonth()
    {
        $this->loadSalaryDetail();
    }
    
    public function render()
    {
        return view('livewire.gaji-pegawai');
    }
}
