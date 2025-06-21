<?php

namespace App\Livewire;

use App\Models\Cuti;
use App\Models\Pegawai;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class CutiManager extends Component
{
    use WithPagination;

    public $pegawai_id;
    public $tanggal_mulai;
    public $tanggal_akhir;
    public $alasan;
    public $cutiId;
    public $isEdit = false;
    public $sisaCuti = null; // New property to store remaining leave days

    protected $rules = [
        'pegawai_id' => 'required|exists:pegawai,id',
        'tanggal_mulai' => 'required|date|after_or_equal:today',
        'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        'alasan' => 'required|min:10'
    ];

    public function render()
    {
        return view('livewire.cuti-manager', [
            'cutis' => Cuti::with('pegawai')->paginate(10),
            'pegawais' => Pegawai::all()
        ]);
    }

    public function updatedPegawaiId($value)
    {
        if (!empty($value)) {
            $this->updateSisaCuti();
        } else {
            $this->sisaCuti = null;
        }
    }

    public function updatedTanggalMulai($value)
    {
        if (!empty($this->pegawai_id) && !empty($value)) {
            $this->updateSisaCuti();
        }
    }

    protected function updateSisaCuti()
    {
        $pegawai = Pegawai::find($this->pegawai_id);
        if ($pegawai) {
            // Determine which year to check (from selected start date or current year)
            $year = !empty($this->tanggal_mulai) 
                ? Carbon::parse($this->tanggal_mulai)->year 
                : Carbon::now()->year;
            
            // Get all approved and pending leave requests for this employee in the specified year
            $cutiTerpakai = $pegawai->getCutiTerpakai($year);
            
            // If we're editing an existing leave request, exclude its days from the count
            // to avoid counting them twice
            if ($this->isEdit && $this->cutiId) {
                $existingCuti = Cuti::find($this->cutiId);
                if ($existingCuti && Carbon::parse($existingCuti->tanggal_mulai)->year == $year 
                    && $existingCuti->status != 'rejected') {
                    $cutiTerpakai -= $existingCuti->durasi;
                }
            }
            
            // Ensure we never have negative days remaining
            $this->sisaCuti = max(0, 12 - $cutiTerpakai);
        }
    }

    public function save()
    {
        if ($this->isEdit) {
            // If editing, update validation rules for dates
            $cuti = Cuti::find($this->cutiId);
            if ($cuti->status !== 'pending') {
                $this->rules['tanggal_mulai'] = 'required|date';
                $this->rules['tanggal_akhir'] = 'required|date|after_or_equal:tanggal_mulai';
            }
        }

        $this->validate();

        try {
            $pegawai = Pegawai::find($this->pegawai_id);
            $startDate = Carbon::parse($this->tanggal_mulai);
            $endDate = Carbon::parse($this->tanggal_akhir);
            $durasi = $startDate->diffInDays($endDate) + 1;
            $year = $startDate->year;
            
            // Calculate the actual leave days already used this year
            $cutiTerpakai = $pegawai->getCutiTerpakai($year);
            
            // If editing, exclude the current leave request from the count
            if ($this->isEdit) {
                $existingCuti = Cuti::find($this->cutiId);
                if ($existingCuti && Carbon::parse($existingCuti->tanggal_mulai)->year == $year 
                    && $existingCuti->status != 'rejected') {
                    $cutiTerpakai -= $existingCuti->durasi;
                }
            }
            
            // Check if this leave request would exceed the annual 12-day limit
            if (($cutiTerpakai + $durasi) > 12) {
                $sisaCuti = 12 - $cutiTerpakai;
                session()->flash('error', "Cuti melebihi batas maksimal! Sisa cuti: {$sisaCuti} hari dari jatah tahunan 12 hari");
                return;
            }

            // Save or update the leave request
            if ($this->isEdit) {
                $cuti = Cuti::find($this->cutiId);
                $cuti->update([
                    'pegawai_id' => $this->pegawai_id,
                    'tanggal_mulai' => $this->tanggal_mulai,
                    'tanggal_akhir' => $this->tanggal_akhir,
                    'alasan' => $this->alasan
                ]);
                session()->flash('message', 'Pengajuan cuti berhasil diupdate!');
            } else {
                Cuti::create([
                    'pegawai_id' => $this->pegawai_id,
                    'tanggal_mulai' => $this->tanggal_mulai,
                    'tanggal_akhir' => $this->tanggal_akhir,
                    'alasan' => $this->alasan,
                    'status' => 'pending'
                ]);
                session()->flash('message', 'Pengajuan cuti berhasil diajukan!');
            }

            $this->reset(['pegawai_id', 'tanggal_mulai', 'tanggal_akhir', 'alasan', 'cutiId', 'isEdit', 'sisaCuti']);
            $this->dispatch('hideModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $cuti = Cuti::find($id);
            $cuti->update(['status' => 'approved']);
            session()->flash('message', 'Cuti berhasil disetujui!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $cuti = Cuti::find($id);
            $cuti->update(['status' => 'rejected']);
            session()->flash('message', 'Cuti berhasil ditolak!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $cuti = Cuti::find($id);
        if (!$cuti) return;

        $this->cutiId = $id;
        $this->pegawai_id = $cuti->pegawai_id;
        $this->tanggal_mulai = $cuti->tanggal_mulai->format('Y-m-d');
        $this->tanggal_akhir = $cuti->tanggal_akhir->format('Y-m-d');
        $this->alasan = $cuti->alasan;
        $this->isEdit = true;
        
        // Update sisa cuti when editing
        $this->updateSisaCuti();
    }

    public function delete($id)
    {
        try {
            Cuti::find($id)->delete();
            session()->flash('message', 'Pengajuan cuti berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset(['pegawai_id', 'tanggal_mulai', 'tanggal_akhir', 'alasan', 'cutiId', 'isEdit', 'sisaCuti']);
    }
}