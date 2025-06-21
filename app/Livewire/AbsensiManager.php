<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiManager extends Component
{
    use WithPagination;
    
    public $pegawai_id;
    public $tanggal;
    public $status;
    public $absensi_id;
    public $isEdit = false;
    public $filter_tanggal;
    public $filter_status;
    public $filter_pegawai_id;

    protected $rules = [
        'pegawai_id' => 'required|exists:pegawai,id',
        'tanggal' => 'required|date',
        'status' => 'required|in:hadir,tidak_hadir,sakit,izin',
    ];

    public function mount()
    {
        $this->tanggal = Carbon::now()->format('Y-m-d');
        $this->filter_tanggal = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Absensi::with('pegawai')->orderBy('tanggal', 'desc');

        if ($this->filter_tanggal) {
            $query->whereDate('tanggal', $this->filter_tanggal);
        }

        if ($this->filter_status) {
            $query->where('status', $this->filter_status);
        }

        if ($this->filter_pegawai_id) {
            $query->where('pegawai_id', $this->filter_pegawai_id);
        }

        return view('livewire.absensi-manager', [
            'absensis' => $query->paginate(10),
            'pegawais' => Pegawai::orderBy('nama')->get(),
            'statusOptions' => [
                'hadir' => 'Hadir',
                'tidak_hadir' => 'Tidak Hadir',
                'sakit' => 'Sakit',
                'izin' => 'Izin'
            ]
        ]);
    }

    public function save()
    {
        $this->validate();

        // Check if an attendance record already exists for this employee on this date
        $existingAbsensi = Absensi::where('pegawai_id', $this->pegawai_id)
            ->whereDate('tanggal', $this->tanggal)
            ->where('id', '!=', $this->absensi_id ?: 0)
            ->first();

        if ($existingAbsensi) {
            session()->flash('error', 'Pegawai ini sudah memiliki data absensi pada tanggal yang sama.');
            return;
        }

        try {
            if ($this->isEdit) {
                $absensi = Absensi::find($this->absensi_id);
                $absensi->update([
                    'pegawai_id' => $this->pegawai_id,
                    'tanggal' => $this->tanggal,
                    'status' => $this->status
                ]);
                session()->flash('message', 'Data absensi berhasil diupdate!');
            } else {
                Absensi::create([
                    'pegawai_id' => $this->pegawai_id,
                    'tanggal' => $this->tanggal,
                    'status' => $this->status
                ]);
                session()->flash('message', 'Data absensi berhasil ditambahkan!');
            }

            $this->resetForm();
            $this->dispatch('hideModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $absensi = Absensi::find($id);
        $this->absensi_id = $id;
        $this->pegawai_id = $absensi->pegawai_id;
        $this->tanggal = $absensi->tanggal->format('Y-m-d');
        $this->status = $absensi->status;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        try {
            Absensi::find($id)->delete();
            session()->flash('message', 'Data absensi berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset(['pegawai_id', 'status', 'absensi_id', 'isEdit']);
        $this->tanggal = Carbon::now()->format('Y-m-d');
    }

    public function bulkAbsensi($status)
    {
        $this->validate([
            'tanggal' => 'required|date',
        ]);

        try {
            // Get all employees
            $pegawais = Pegawai::all();
            $tanggal = $this->tanggal;
            $count = 0;

            foreach ($pegawais as $pegawai) {
                // Check if attendance already exists for this employee on this date
                $existingAbsensi = Absensi::where('pegawai_id', $pegawai->id)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                if (!$existingAbsensi) {
                    Absensi::create([
                        'pegawai_id' => $pegawai->id,
                        'tanggal' => $tanggal,
                        'status' => $status
                    ]);
                    $count++;
                }
            }

            if ($count > 0) {
                session()->flash('message', "Berhasil menambahkan absensi untuk $count pegawai dengan status: " . ucfirst(str_replace('_', ' ', $status)));
            } else {
                session()->flash('error', 'Tidak ada absensi baru yang ditambahkan. Semua pegawai sudah memiliki data absensi untuk tanggal ini.');
            }

            $this->filter_tanggal = $tanggal;
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}
