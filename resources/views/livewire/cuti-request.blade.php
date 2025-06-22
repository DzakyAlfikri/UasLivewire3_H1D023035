<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Pengajuan Cuti</h1>
    
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <p>{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6">
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-white">Form Pengajuan Cuti</h2>
                    <p class="text-gray-400 text-sm mt-1">Silakan isi form untuk mengajukan cuti</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center">
                        <div class="text-right mr-4">
                            <div class="text-sm text-gray-400">Sisa Cuti</div>
                            <div class="text-2xl font-bold {{ $sisaCuti < 3 ? 'text-red-500' : 'text-green-500' }}">{{ $sisaCuti }} Hari</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br {{ $sisaCuti < 3 ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' }} flex items-center justify-center">
                            <i class="fas {{ $sisaCuti < 3 ? 'fa-exclamation' : 'fa-check' }} text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="pegawai" class="block text-sm text-gray-400 mb-1">Pegawai</label>
                    <div class="bg-gray-700/50 rounded-xl p-4 border border-gray-700 text-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium">{{ $pegawai->nama ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $pegawai->nip ?? 'N/A' }} - {{ $pegawai->jabatan->nama_jabatan ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="durasi" class="block text-sm text-gray-400 mb-1">Durasi Cuti</label>
                    <div class="bg-gray-700/50 rounded-xl p-4 border border-gray-700">
                        <div class="flex justify-between items-center">
                            <div class="text-3xl font-bold text-white">{{ $durasi }}</div>
                            <div class="text-gray-400">Hari Kerja</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">*Tidak termasuk hari Sabtu dan Minggu</div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="tanggal_mulai" class="block text-sm text-gray-400 mb-1">Tanggal Mulai</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-500"></i>
                        </div>
                        <input wire:model.live="tanggal_mulai" type="date" id="tanggal_mulai" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-3 pl-10 pr-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                    </div>
                    @error('tanggal_mulai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="tanggal_akhir" class="block text-sm text-gray-400 mb-1">Tanggal Akhir</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-500"></i>
                        </div>
                        <input wire:model.live="tanggal_akhir" type="date" id="tanggal_akhir" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-3 pl-10 pr-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                    </div>
                    @error('tanggal_akhir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label for="alasan" class="block text-sm text-gray-400 mb-1">Alasan Cuti</label>
                <textarea wire:model="alasan" id="alasan" rows="4" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-3 px-4 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Berikan alasan pengajuan cuti..."></textarea>
                @error('alasan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex justify-end">
                <button wire:click="save" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 px-6 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 transition-all duration-200 flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i> Ajukan Cuti
                </button>
            </div>
        </div>
    </div>
    
    <!-- Recent Leave Requests -->
    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
        <h2 class="text-xl font-semibold text-white mb-6">Riwayat Pengajuan Cuti</h2>
        
        @php
            $recentCuti = $pegawai ? App\Models\Cuti::where('pegawai_id', $pegawai->id)
                ->latest()
                ->take(5)
                ->get() : collect();
        @endphp
        
        @if($recentCuti->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-700">
                            <th class="pb-3">Tanggal Pengajuan</th>
                            <th class="pb-3">Periode</th>
                            <th class="pb-3">Durasi</th>
                            <th class="pb-3">Alasan</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCuti as $cuti)
                        <tr class="border-b border-gray-800">
                            <td class="py-4">{{ $cuti->created_at->format('d/m/Y') }}</td>
                            <td class="py-4">{{ Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($cuti->tanggal_akhir)->format('d/m/Y') }}</td>
                            <td class="py-4">{{ $cuti->durasi }} hari</td>
                            <td class="py-4 max-w-xs truncate">{{ $cuti->alasan }}</td>
                            <td class="py-4">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    {{ $cuti->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($cuti->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($cuti->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-3"></i>
                <p>Belum ada riwayat pengajuan cuti</p>
            </div>
        @endif
    </div>
</div>
