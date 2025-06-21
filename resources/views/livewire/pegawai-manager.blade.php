<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Manajemen Pegawai</h1>
    
    <div class="flex justify-between items-center mb-6">
        <div class="text-lg text-gray-300">
            <span>Total pegawai: <strong>{{ $pegawais->total() }}</strong></span>
        </div>
        <button type="button" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 transition-all flex items-center space-x-2" onclick="event.preventDefault(); showAddModal();">
            <i class="fas fa-plus"></i>
            <span>Tambah Pegawai</span>
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-900/20 text-green-400 border border-green-900/30 rounded-xl p-4 mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span>{{ session('message') }}</span>
            <button type="button" class="ml-auto text-green-500 hover:text-green-300" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-700">
                        <th class="pb-3 px-2">NIP</th>
                        <th class="pb-3 px-2">Nama</th>
                        <th class="pb-3 px-2">Jabatan</th>
                        <th class="pb-3 px-2">Unit Kerja</th>
                        <th class="pb-3 px-2">Gaji Pokok</th>
                        <th class="pb-3 px-2">Tunjangan</th>
                        <th class="pb-3 px-2">Gaji Total</th>
                        <th class="pb-3 px-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawais as $pegawai)
                    <tr class="border-b border-gray-800">
                        <td class="py-4 px-2">{{ $pegawai->nip }}</td>
                        <td class="py-4 px-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mr-3">
                                    <span class="text-white font-medium">{{ strtoupper(substr($pegawai->nama, 0, 1)) }}</span>
                                </div>
                                <span>{{ $pegawai->nama }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-2">{{ $pegawai->jabatan->nama_jabatan }}</td>
                        <td class="py-4 px-2">{{ $pegawai->unitKerja->nama_unit }}</td>
                        <td class="py-4 px-2">Rp {{ number_format($pegawai->gaji, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">Rp {{ number_format($pegawai->jabatan->tunjangan, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">
                            <span class="font-semibold text-orange-400">
                                Rp {{ number_format($pegawai->gaji_total, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-right">
                            <button class="bg-amber-500/20 text-amber-400 hover:bg-amber-500/40 transition-all p-2 rounded-lg mr-2" onclick="event.preventDefault(); showEditModal({{ $pegawai->id }});">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-500/20 text-red-400 hover:bg-red-500/40 transition-all p-2 rounded-lg" wire:click="delete({{ $pegawai->id }})" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $pegawais->links() }}
        </div>
    </div>

    <!-- Custom Modal Overlay - Initially hidden -->
    <div id="customModal" class="fixed inset-0 z-50 hidden" wire:ignore.self>
        <!-- Semi-transparent backdrop -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl" onclick="event.stopPropagation();">
            <div class="bg-gray-800 rounded-3xl shadow-2xl border border-gray-700 overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700">
                    <h5 class="text-xl font-semibold text-white">{{ $isEdit ? 'Edit' : 'Tambah' }} Pegawai</h5>
                    <button type="button" class="text-gray-400 hover:text-white" onclick="event.preventDefault(); closeModal();">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <form wire:submit="save">
                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        <div class="bg-gray-700/30 p-5 rounded-xl mb-5">
                            <h6 class="text-orange-400 font-medium mb-3">Informasi Pegawai</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">NIP</label>
                                    <input type="text" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('nip') border-red-500 @enderror" wire:model="nip">
                                    @error('nip') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Nama</label>
                                    <input type="text" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('nama') border-red-500 @enderror" wire:model="nama">
                                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-700/30 p-5 rounded-xl mb-5">
                            <h6 class="text-orange-400 font-medium mb-3">Posisi & Penempatan</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Jabatan</label>
                                    <select class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('jabatan_id') border-red-500 @enderror" wire:model="jabatan_id">
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jabatans as $jabatan)
                                            <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
                                        @endforeach
                                    </select>
                                    @error('jabatan_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Unit Kerja</label>
                                    <select class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('unit_kerja_id') border-red-500 @enderror" wire:model="unit_kerja_id">
                                        <option value="">Pilih Unit Kerja</option>
                                        @foreach($unitKerjas as $unitKerja)
                                            <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama_unit }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit_kerja_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-700/30 p-5 rounded-xl">
                            <h6 class="text-orange-400 font-medium mb-3">Informasi Gaji</h6>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Gaji Pokok (Rp)</label>
                                <input type="number" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('gaji') border-red-500 @enderror" wire:model="gaji">
                                @error('gaji') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @if($jabatan_id && $gaji)
                            <div class="mt-3 bg-gray-800/50 p-3 rounded-xl">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Gaji Pokok:</span>
                                    <span class="text-white">Rp {{ number_format($gaji, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-400">Tunjangan:</span>
                                    <span class="text-white">Rp {{ number_format(optional($jabatans->find($jabatan_id))->tunjangan ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-gray-700 my-2"></div>
                                <div class="flex justify-between font-medium">
                                    <span class="text-gray-300">Total:</span>
                                    <span class="text-orange-400">Rp {{ number_format($gaji + (optional($jabatans->find($jabatan_id))->tunjangan ?? 0), 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-700">
                        <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-colors" onclick="closeModal()">
                            Batal
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 transition-all">
                            {{ $isEdit ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show and hide modal functions
        function showAddModal() {
            // First reset the form
            @this.resetForm();
            
            // Then show the modal immediately
            document.getElementById('customModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        
        function showEditModal(id) {
            // Call the edit method
            @this.edit(id);
            
            // Then show the modal immediately
            document.getElementById('customModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        
        function closeModal() {
            document.getElementById('customModal').classList.add('hidden');
            document.body.style.overflow = ''; // Re-enable scrolling
        }
        
        // Listen for Livewire events
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('hideModal', () => {
                closeModal();
            });
        });
    </script>
</div>