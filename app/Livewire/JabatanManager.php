<?php

namespace App\Livewire;

use App\Models\Jabatan;
use Livewire\Component;
use Livewire\WithPagination;

class JabatanManager extends Component
{
    use WithPagination;

    public $nama_jabatan;
    public $tunjangan;
    public $jabatanId;
    public $isEdit = false;

    protected $rules = [
        'nama_jabatan' => 'required|min:3',
        'tunjangan' => 'required|numeric|min:0'
    ];

    public function render()
    {
        return view('livewire.jabatan-manager', [
            'jabatans' => Jabatan::withCount('pegawai')->paginate(10)
        ]);
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                $jabatan = Jabatan::find($this->jabatanId);
                $jabatan->update([
                    'nama_jabatan' => $this->nama_jabatan,
                    'tunjangan' => $this->tunjangan
                ]);
                session()->flash('message', 'Jabatan berhasil diupdate!');
            } else {
                Jabatan::create([
                    'nama_jabatan' => $this->nama_jabatan,
                    'tunjangan' => $this->tunjangan
                ]);
                session()->flash('message', 'Jabatan berhasil ditambahkan!');
            }

            $this->reset(['nama_jabatan', 'tunjangan', 'jabatanId', 'isEdit']);
            $this->dispatch('hideModal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $jabatan = Jabatan::find($id);
        if (!$jabatan) return;
        
        $this->jabatanId = $id;
        $this->nama_jabatan = $jabatan->nama_jabatan;
        $this->tunjangan = $jabatan->tunjangan;
        $this->isEdit = true;
    }

    public function resetForm()
    {
        $this->reset(['nama_jabatan', 'tunjangan', 'jabatanId', 'isEdit']);
    }

    public function delete($id)
    {
        try {
            $jabatan = Jabatan::find($id);
            
            // Check if there are any pegawai with this jabatan
            if ($jabatan->pegawai->count() > 0) {
                session()->flash('error', 'Tidak dapat menghapus jabatan yang masih digunakan oleh pegawai.');
                return;
            }
            
            $jabatan->delete();
            session()->flash('message', 'Jabatan berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}