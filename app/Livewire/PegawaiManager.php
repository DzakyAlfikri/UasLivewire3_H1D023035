<?php

namespace App\Livewire;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PegawaiManager extends Component
{
    use WithPagination;

    public $nip;
    public $nama;
    public $jabatan_id;
    public $unit_kerja_id;
    public $gaji;
    public $pegawaiId;
    public $isEdit = false;

    protected $rules = [
        'nip' => 'required|unique:pegawai,nip',
        'nama' => 'required|min:3',
        'jabatan_id' => 'required|exists:jabatan,id',
        'unit_kerja_id' => 'required|exists:unit_kerja,id',
        'gaji' => 'required|numeric|min:0'
    ];

    public function render()
    {
        return view('livewire.pegawai-manager', [
            'pegawais' => Pegawai::with(['jabatan', 'unitKerja'])->paginate(10),
            'jabatans' => Jabatan::all(),
            'unitKerjas' => UnitKerja::all()
        ]);
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->rules['nip'] = 'required|unique:pegawai,nip,' . $this->pegawaiId;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $pegawai = Pegawai::find($this->pegawaiId);
                $pegawai->update([
                    'nip' => $this->nip,
                    'nama' => $this->nama,
                    'jabatan_id' => $this->jabatan_id,
                    'unit_kerja_id' => $this->unit_kerja_id,
                    'gaji' => $this->gaji
                ]);
                session()->flash('message', 'Pegawai berhasil diupdate!');
            } else {
                Pegawai::create([
                    'nip' => $this->nip,
                    'nama' => $this->nama,
                    'jabatan_id' => $this->jabatan_id,
                    'unit_kerja_id' => $this->unit_kerja_id,
                    'gaji' => $this->gaji
                ]);
                session()->flash('message', 'Pegawai berhasil ditambahkan!');
            }

            $this->reset(['nip', 'nama', 'jabatan_id', 'unit_kerja_id', 'gaji', 'pegawaiId', 'isEdit']);
            $this->dispatch('hideModal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) return;
        
        $this->pegawaiId = $id;
        $this->nip = $pegawai->nip;
        $this->nama = $pegawai->nama;
        $this->jabatan_id = $pegawai->jabatan_id;
        $this->unit_kerja_id = $pegawai->unit_kerja_id;
        $this->gaji = $pegawai->gaji;
        $this->isEdit = true;
    }

    public function resetForm()
    {
        $this->reset(['nip', 'nama', 'jabatan_id', 'unit_kerja_id', 'gaji', 'pegawaiId', 'isEdit']);
    }

    /**
     * Delete the specified employee.
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            $pegawai = Pegawai::findOrFail($id);
            
            // Check if the employee has related data
            if ($pegawai->absensi()->count() > 0 || $pegawai->cuti()->count() > 0) {
                session()->flash('error', 'Tidak dapat menghapus pegawai yang memiliki data absensi atau cuti. Nonaktifkan pegawai sebagai alternatif.');
                return;
            }
            
            // If employee has a user account, delete or detach it
            if ($pegawai->user_id) {
                $user = User::find($pegawai->user_id);
                if ($user) {
                    // Option 1: Delete the user account
                    $user->delete();
                    
                    // Option 2 (alternative): Just detach the relationship
                    // $pegawai->user_id = null;
                    // $pegawai->save();
                }
            }
            
            // Now delete the employee
            $pegawai->delete();
            
            DB::commit();
            
            session()->flash('message', 'Pegawai berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Error deleting employee: ' . $e->getMessage());
            
            session()->flash('error', 'Terjadi kesalahan saat menghapus pegawai: ' . $e->getMessage());
        }
    }
}