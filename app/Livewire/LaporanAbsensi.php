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

    public function mount()
    {
        $this->bulan = Carbon::now()->month;
        $this->tahun = Carbon::now()->year;
    }

    public function render()
    {
        $query = Absensi::with('pegawai')
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun);

        if ($this->pegawai_id) {
            $query->where('pegawai_id', $this->pegawai_id);
        }

        $absensi = $query->get();

        // Rekapitulasi per pegawai
        $rekapitulasi = $absensi->groupBy('pegawai_id')->map(function ($items, $pegawaiId) {
            $pegawai = Pegawai::find($pegawaiId);
            return [
                'pegawai' => $pegawai,
                'hadir' => $items->where('status', 'hadir')->count(),
                'tidak_hadir' => $items->where('status', 'tidak_hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'total' => $items->count()
            ];
        });

        return view('livewire.laporan-absensi', [
            'rekapitulasi' => $rekapitulasi,
            'pegawais' => Pegawai::all(),
            'bulanArray' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ]
        ]);
    }
}