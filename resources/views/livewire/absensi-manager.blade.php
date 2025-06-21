<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-purple-600 mb-6">Manajemen Absensi</h1>
    
    <div class="flex justify-between items-center mb-6">
        <div class="text-lg text-gray-300">
            <span>Total absensi: <strong>{{ $absensis->total() }}</strong></span>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-green-500/20 transition-all flex items-center space-x-2" onclick="event.preventDefault(); showBulkModal();">
                <i class="fas fa-users"></i>
                <span>Absensi Massal</span>
            </button>
            <button type="button" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-purple-500/20 transition-all flex items-center space-x-2" onclick="event.preventDefault(); showAddModal();">
                <i class="fas fa-plus"></i>
                <span>Tambah Absensi</span>
            </button>
        </div>
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

    @if (session()->has('error'))
        <div class="bg-red-900/20 text-red-400 border border-red-900/30 rounded-xl p-4 mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="ml-auto text-red-500 hover:text-red-300" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-white mb-4">Filter Data Absensi</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Tanggal</label>
                <input type="date" wire:model.live="filter_tanggal" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Status</label>
                <select wire:model.live="filter_status" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Pegawai</label>
                <select wire:model.live="filter_pegawai_id" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="">Semua Pegawai</option>
                    @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-700">
                        <th class="pb-3 px-2">Tanggal</th>
                        <th class="pb-3 px-2">NIP</th>
                        <th class="pb-3 px-2">Nama</th>
                        <th class="pb-3 px-2">Jabatan</th>
                        <th class="pb-3 px-2">Status</th>
                        <th class="pb-3 px-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($absensis->count() > 0)
                        @foreach($absensis as $absensi)
                        <tr class="border-b border-gray-800">
                            <td class="py-4 px-2">{{ $absensi->tanggal->format('d/m/Y') }}</td>
                            <td class="py-4 px-2">{{ $absensi->pegawai->nip }}</td>
                            <td class="py-4 px-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mr-3">
                                        <span class="text-white font-medium">{{ strtoupper(substr($absensi->pegawai->nama, 0, 1)) }}</span>
                                    </div>
                                    <span>{{ $absensi->pegawai->nama }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-2">{{ $absensi->pegawai->jabatan->nama_jabatan }}</td>
                            <td class="py-4 px-2">
                                @if($absensi->status == 'hadir')
                                    <span class="bg-green-900/20 text-green-500 py-1 px-2.5 rounded-lg font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Hadir
                                    </span>
                                @elseif($absensi->status == 'tidak_hadir')
                                    <span class="bg-red-900/20 text-red-500 py-1 px-2.5 rounded-lg font-medium">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Hadir
                                    </span>
                                @elseif($absensi->status == 'sakit')
                                    <span class="bg-yellow-900/20 text-yellow-500 py-1 px-2.5 rounded-lg font-medium">
                                        <i class="fas fa-thermometer-half mr-1"></i> Sakit
                                    </span>
                                @elseif($absensi->status == 'izin')
                                    <span class="bg-blue-900/20 text-blue-500 py-1 px-2.5 rounded-lg font-medium">
                                        <i class="fas fa-calendar-check mr-1"></i> Izin
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-2 text-right">
                                <button class="bg-amber-500/20 text-amber-400 hover:bg-amber-500/40 transition-all p-2 rounded-lg mr-1" onclick="event.preventDefault(); showEditModal({{ $absensi->id }});">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="bg-red-500/20 text-red-400 hover:bg-red-500/40 transition-all p-2 rounded-lg" wire:click="delete({{ $absensi->id }})" onclick="return confirm('Yakin ingin menghapus data absensi ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="py-4 px-2 text-center text-gray-400">Tidak ada data absensi yang ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $absensis->links() }}
        </div>
    </div>

    <!-- Custom Modal for Add/Edit Attendance -->
    <div id="customModal" class="fixed inset-0 z-50 hidden" wire:ignore.self>
        <!-- Semi-transparent backdrop -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-xl" onclick="event.stopPropagation();">
            <div class="bg-gray-800 rounded-3xl shadow-2xl border border-gray-700 overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700">
                    <h5 class="text-xl font-semibold text-white">{{ $isEdit ? 'Edit' : 'Tambah' }} Absensi</h5>
                    <button type="button" class="text-gray-400 hover:text-white" onclick="event.preventDefault(); closeModal();">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <form wire:submit="save">
                    <div class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm text-gray-400 mb-1">Pegawai</label>
                            <select class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 @error('pegawai_id') @enderror" wire:model="pegawai_id">
                                <option value="">Pilih Pegawai</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }} ({{ $pegawai->nip }})</option>
                                @endforeach
                            </select>
                            @error('pegawai_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm text-gray-400 mb-1">Tanggal</label>
                            <input type="date" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 @error('tanggal') @enderror" wire:model="tanggal">
                            @error('tanggal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Status</label>
                            <select class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 @error('status') border-red-500 @enderror" wire:model="status">
                                <option value="">Pilih Status</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-700">
                        <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-colors" onclick="closeModal()">
                            Batal
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-purple-500/20 transition-all">
                            {{ $isEdit ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Modal for Bulk Attendance -->
    <div id="bulkModal" class="fixed inset-0 z-50 hidden" wire:ignore.self>
        <!-- Semi-transparent backdrop -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="closeBulkModal()"></div>
        
        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg" onclick="event.stopPropagation();">
            <div class="bg-gray-800 rounded-3xl shadow-2xl border border-gray-700 overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700">
                    <h5 class="text-xl font-semibold text-white">Absensi Massal</h5>
                    <button type="button" class="text-gray-400 hover:text-white" onclick="event.preventDefault(); closeBulkModal();">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="mb-5">
                        <label class="block text-sm text-gray-400 mb-1">Tanggal</label>
                        <input type="date" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('tanggal') border-red-500 @enderror" wire:model="tanggal">
                        @error('tanggal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="bg-blue-900/20 text-blue-400 border border-blue-900/30 rounded-xl p-4 mb-5">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Pilih status absensi yang akan diterapkan untuk semua pegawai yang belum memiliki absensi pada tanggal tersebut.</span>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <button type="button" class="bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-xl hover:shadow-lg hover:shadow-green-500/20 transition-all flex items-center justify-center space-x-2" wire:click="bulkAbsensi('hadir')" onclick="closeBulkModal()">
                            <i class="fas fa-check text-xl"></i>
                            <span class="font-medium">Semua Hadir</span>
                        </button>
                        <button type="button" class="bg-gradient-to-r from-red-500 to-red-600 text-white py-3 px-4 rounded-xl hover:shadow-lg hover:shadow-red-500/20 transition-all flex items-center justify-center space-x-2" wire:click="bulkAbsensi('tidak_hadir')" onclick="closeBulkModal()">
                            <i class="fas fa-times text-xl"></i>
                            <span class="font-medium">Semua Tidak Hadir</span>
                        </button>
                        <button type="button" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white py-3 px-4 rounded-xl hover:shadow-lg hover:shadow-yellow-500/20 transition-all flex items-center justify-center space-x-2" wire:click="bulkAbsensi('sakit')" onclick="closeBulkModal()">
                            <i class="fas fa-thermometer-half text-xl"></i>
                            <span class="font-medium">Semua Sakit</span>
                        </button>
                        <button type="button" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-xl hover:shadow-lg hover:shadow-blue-500/20 transition-all flex items-center justify-center space-x-2" wire:click="bulkAbsensi('izin')" onclick="closeBulkModal()">
                            <i class="fas fa-calendar-check text-xl"></i>
                            <span class="font-medium">Semua Izin</span>
                        </button>
                    </div>
                </div>
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
        
        function showBulkModal() {
            document.getElementById('bulkModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        
        function closeBulkModal() {
            document.getElementById('bulkModal').classList.add('hidden');
            document.body.style.overflow = ''; // Re-enable scrolling
        }
        
        // Listen for Livewire events
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('hideModal', () => {
                closeModal();
            });
            
            Livewire.on('closeModal', () => {
                closeModal();
            });
        });
    </script>
</div>
