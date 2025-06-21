<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Manajemen Cuti</h1>
    
    <div class="flex justify-between items-center mb-6">
        <div class="text-lg text-gray-300">
            <span>Total pengajuan: <strong>{{ $cutis->total() }}</strong></span>
        </div>
        <button type="button" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 transition-all flex items-center space-x-2" onclick="event.preventDefault(); showAddModal();">
            <i class="fas fa-plus"></i>
            <span>Ajukan Cuti</span>
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

    @if (session()->has('error'))
        <div class="bg-red-900/20 text-red-400 border border-red-900/30 rounded-xl p-4 mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="ml-auto text-red-500 hover:text-red-300" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-700">
                        <th class="pb-3 px-2">Pegawai</th>
                        <th class="pb-3 px-2">Tanggal Mulai</th>
                        <th class="pb-3 px-2">Tanggal Akhir</th>
                        <th class="pb-3 px-2">Durasi</th>
                        <th class="pb-3 px-2">Sisa Cuti</th>
                        <th class="pb-3 px-2">Alasan</th>
                        <th class="pb-3 px-2">Status</th>
                        <th class="pb-3 px-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cutis as $cuti)
                    <tr class="border-b border-gray-800">
                        <td class="py-4 px-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mr-3">
                                    <span class="text-white font-medium">{{ strtoupper(substr($cuti->pegawai->nama, 0, 1)) }}</span>
                                </div>
                                <span>{{ $cuti->pegawai->nama }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-2">{{ $cuti->tanggal_mulai->format('d/m/Y') }}</td>
                        <td class="py-4 px-2">{{ $cuti->tanggal_akhir->format('d/m/Y') }}</td>
                        <td class="py-4 px-2">
                            <span class="bg-gray-700/50 text-gray-300 py-1 px-2 rounded-lg">
                                {{ $cuti->durasi }} hari
                            </span>
                        </td>
                        <td class="py-4 px-2">
                            @php
                                // Get the year from the leave request
                                $leaveYear = $cuti->tanggal_mulai->year;
                                
                                // Calculate total used leave in this year
                                $totalUsedLeave = $cuti->pegawai->getCutiTerpakai($leaveYear);
                                
                                // Don't count rejected leaves against quota
                                if ($cuti->status == 'rejected') {
                                    $totalUsedLeave -= $cuti->durasi;
                                }
                                
                                // Calculate remaining days based on annual 12-day allocation
                                $sisaCutiPegawai = max(0, 12 - $totalUsedLeave);
                            @endphp
                            <span class="bg-gray-700/50 {{ $sisaCutiPegawai > 3 ? 'text-green-400' : ($sisaCutiPegawai > 0 ? 'text-yellow-400' : 'text-red-400') }} py-1 px-2 rounded-lg">
                                {{ $sisaCutiPegawai }} hari
                            </span>
                        </td>
                        <td class="py-4 px-2">{{ Str::limit($cuti->alasan, 50) }}</td>
                        <td class="py-4 px-2">
                            @if($cuti->status == 'approved')
                                <span class="bg-green-900/20 text-green-500 py-1 px-2.5 rounded-lg font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Disetujui
                                </span>
                            @elseif($cuti->status == 'rejected')
                                <span class="bg-red-900/20 text-red-500 py-1 px-2.5 rounded-lg font-medium">
                                    <i class="fas fa-times-circle mr-1"></i> Ditolak
                                </span>
                            @else
                                <span class="bg-yellow-900/20 text-yellow-500 py-1 px-2.5 rounded-lg font-medium">
                                    <i class="fas fa-clock mr-1"></i> Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-2 text-right whitespace-nowrap">
                            @if($cuti->status == 'pending')
                                <button class="bg-green-500/20 text-green-400 hover:bg-green-500/40 transition-all p-2 rounded-lg mr-1" wire:click="approve({{ $cuti->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="bg-red-500/20 text-red-400 hover:bg-red-500/40 transition-all p-2 rounded-lg mr-1" wire:click="reject({{ $cuti->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                            <button class="bg-amber-500/20 text-amber-400 hover:bg-amber-500/40 transition-all p-2 rounded-lg mr-1" onclick="event.preventDefault(); showEditModal({{ $cuti->id }});">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-500/20 text-red-400 hover:bg-red-500/40 transition-all p-2 rounded-lg" wire:click="delete({{ $cuti->id }})" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $cutis->links() }}
        </div>
    </div>

    <!-- Custom Modal Overlay - Initially hidden -->
    <div id="customModal" class="fixed inset-0 z-50 hidden" wire:ignore.self>
        <!-- Semi-transparent backdrop -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl" onclick="event.stopPropagation();">
            <div class="bg-gray-800 rounded-3xl shadow-2xl border border-gray-700 overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700">
                    <h5 class="text-xl font-semibold text-white">{{ $isEdit ? 'Edit' : 'Ajukan' }} Cuti</h5>
                    <button type="button" class="text-gray-400 hover:text-white" onclick="event.preventDefault(); closeModal();">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <form wire:submit="save">
                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        <div class="bg-gray-700/30 p-5 rounded-xl mb-5">
                            <h6 class="text-orange-400 font-medium mb-3">Informasi Pegawai</h6>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Pegawai</label>
                                <select class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('pegawai_id') border-red-500 @enderror" wire:model.live="pegawai_id">
                                    <option value="">Pilih Pegawai</option>
                                    @foreach($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id }}">{{ $pegawai->nama }} ({{ $pegawai->nip }})</option>
                                    @endforeach
                                </select>
                                @error('pegawai_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                
                                @if($sisaCuti !== null)
                                <div class="mt-2 flex items-center">
                                    <i class="fas fa-calendar-check text-blue-400 mr-2"></i>
                                    <span class="text-sm">
                                        Sisa cuti: 
                                        <span class="{{ $sisaCuti > 0 ? 'text-green-400' : 'text-red-400' }} font-semibold">
                                            {{ $sisaCuti }} hari
                                        </span>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-gray-700/30 p-5 rounded-xl mb-5">
                            <h6 class="text-orange-400 font-medium mb-3">Periode Cuti</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Tanggal Mulai</label>
                                    <input type="date" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('tanggal_mulai') border-red-500 @enderror" wire:model="tanggal_mulai">
                                    @error('tanggal_mulai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Tanggal Akhir</label>
                                    <input type="date" class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('tanggal_akhir') border-red-500 @enderror" wire:model="tanggal_akhir">
                                    @error('tanggal_akhir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-700/30 p-5 rounded-xl">
                            <h6 class="text-orange-400 font-medium mb-3">Informasi Cuti</h6>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Alasan</label>
                                <textarea class="w-full rounded-xl bg-gray-800/80 border text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('alasan') border-red-500 @enderror" wire:model="alasan" rows="3"></textarea>
                                @error('alasan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-700">
                        <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-colors" onclick="closeModal()">
                            Batal
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 transition-all">
                            {{ $isEdit ? 'Update' : 'Ajukan' }}
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