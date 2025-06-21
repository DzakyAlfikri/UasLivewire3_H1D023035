<?php

namespace App\Livewire;

use App\Models\UnitKerja;
use Livewire\Component;
use Livewire\WithPagination;

class UnitKerjaManager extends Component
{
    use WithPagination;

    public $nama_unit;
    public $lokasi;
    public $unitKerjaId;
    public $isEdit = false;

    protected $rules = [
        'nama_unit' => 'required|min:3',
        'lokasi' => 'required|min:3'
    ];

    public function render()
    {
        return view('livewire.unit-kerja-manager', [
            'unitKerjas' => UnitKerja::with('pegawai')->paginate(10)
        ]);
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                $unitKerja = UnitKerja::find($this->unitKerjaId);
                $unitKerja->update([
                    'nama_unit' => $this->nama_unit,
                    'lokasi' => $this->lokasi
                ]);
                session()->flash('message', 'Unit Kerja berhasil diupdate!');
            } else {
                UnitKerja::create([
                    'nama_unit' => $this->nama_unit,
                    'lokasi' => $this->lokasi
                ]);
                session()->flash('message', 'Unit Kerja berhasil ditambahkan!');
            }

            $this->reset(['nama_unit', 'lokasi', 'unitKerjaId', 'isEdit']);
            $this->dispatch('hideModal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $unitKerja = UnitKerja::find($id);
        if (!$unitKerja) return;
        
        $this->unitKerjaId = $id;
        $this->nama_unit = $unitKerja->nama_unit;
        $this->lokasi = $unitKerja->lokasi;
        $this->isEdit = true;
    }

    public function resetForm()
    {
        $this->reset(['nama_unit', 'lokasi', 'unitKerjaId', 'isEdit']);
    }

    public function delete($id)
    {
        try {
            $unitKerja = UnitKerja::find($id);
            
            // Check if there are any pegawai in this unit
            if ($unitKerja->pegawai->count() > 0) {
                session()->flash('error', 'Tidak dapat menghapus unit kerja yang masih memiliki pegawai.');
                return;
            }
            
            $unitKerja->delete();
            session()->flash('message', 'Unit Kerja berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}