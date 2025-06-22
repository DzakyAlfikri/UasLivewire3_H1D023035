<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600 mb-6">Laporan Rekapitulasi Absensi</h1>
    
    <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div>
                <label for="bulan" class="block text-sm text-gray-400 mb-1">Bulan</label>
                <select wire:model.live="bulan" id="bulan" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @foreach($bulanArray as $key => $namaBulan)
                        <option value="{{ $key }}">{{ $namaBulan }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="tahun" class="block text-sm text-gray-400 mb-1">Tahun</label>
                <select wire:model.live="tahun" id="tahun" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @for($i = Carbon\Carbon::now()->year; $i >= (Carbon\Carbon::now()->year - 2); $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label for="pegawai_id" class="block text-sm text-gray-400 mb-1">Pegawai</label>
                <select wire:model.live="pegawai_id" id="pegawai_id" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Pegawai</option>
                    @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6 print:hidden">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white shadow-lg shadow-green-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $workingDays }}</h2>
                        <p class="text-sm text-green-100">Total Hari Kerja</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-lg shadow-blue-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $rekapitulasi->sum('hadir') }}</h2>
                        <p class="text-sm text-blue-100">Total Kehadiran</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-4 text-white shadow-lg shadow-yellow-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $rekapitulasi->sum('izin') + $rekapitulasi->sum('sakit') }}</h2>
                        <p class="text-sm text-yellow-100">Total Izin & Sakit</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-exclamation text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 text-white shadow-lg shadow-red-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $rekapitulasi->sum('tidak_hadir') }}</h2>
                        <p class="text-sm text-red-100">Total Alpha</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-times text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 print:shadow-none print:border-0">
        <h2 class="text-xl font-semibold text-white mb-6 print:text-black">Rekapitulasi Absensi Bulan {{ $bulanArray[$bulan] }} {{ $tahun }}</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full print:text-black">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-700 print:text-gray-700 print:border-gray-300">
                        <th class="pb-3">No</th>
                        <th class="pb-3">NIP</th>
                        <th class="pb-3">Nama Pegawai</th>
                        <th class="pb-3">Jabatan</th>
                        <th class="pb-3">Unit Kerja</th>
                        <th class="pb-3 text-center">Hadir</th>
                        <th class="pb-3 text-center">Sakit</th>
                        <th class="pb-3 text-center">Izin</th>
                        <th class="pb-3 text-center">Alpha</th>
                        <th class="pb-3 print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapitulasi as $index => $data)
                        <tr class="border-b border-gray-800 print:border-gray-300">
                            <td class="py-4">{{ $index + 1 }}</td>
                            <td class="py-4">{{ $data['pegawai']->nip }}</td>
                            <td class="py-4">{{ $data['pegawai']->nama }}</td>
                            <td class="py-4">{{ $data['pegawai']->jabatan->nama_jabatan ?? 'N/A' }}</td>
                            <td class="py-4">{{ $data['pegawai']->unitKerja->nama_unit ?? 'N/A' }}</td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    {{ $data['hadir'] }}
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                                    {{ $data['sakit'] }}
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                    {{ $data['izin'] }}
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                    {{ $data['tidak_hadir'] }}
                                </span>
                            </td>
                            <td class="py-4 text-center print:hidden">
                                <button wire:click="showAttendanceDetail({{ $data['pegawai']->id }})" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Detail Modal -->
    @if($showDetailModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-3xl w-full max-w-5xl max-h-[90vh] overflow-auto border border-gray-700 shadow-xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-t-3xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Detail Absensi Pegawai</h2>
                        <p class="text-blue-100">{{ $bulanArray[$bulan] }} {{ $tahun }}</p>
                    </div>
                    <button wire:click="closeDetailModal" class="bg-white/20 p-2 rounded-full hover:bg-white/30 transition-colors">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                @if($selectedPegawai)
                <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-700 text-white mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">{{ $selectedPegawai->nama }}</div>
                            <div class="text-sm text-gray-400">{{ $selectedPegawai->nip }} - {{ $selectedPegawai->jabatan->nama_jabatan ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                        <span class="text-sm text-gray-400">Hadir</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                        <span class="text-sm text-gray-400">Izin</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-orange-500 mr-2"></div>
                        <span class="text-sm text-gray-400">Sakit</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                        <span class="text-sm text-gray-400">Alpha</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-gray-500 mr-2"></div>
                        <span class="text-sm text-gray-400">Akhir Pekan</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 border-b border-gray-700">
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Hari</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">Waktu</th>
                                <th class="pb-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailedAttendance as $record)
                                <tr class="border-b border-gray-800 {{ $record['is_weekend'] ? 'bg-gray-800/30' : '' }}">
                                    <td class="py-3">{{ $record['formatted_date'] }}</td>
                                    <td class="py-3 {{ $record['is_weekend'] ? 'text-orange-400' : '' }}">{{ $record['day'] }}</td>
                                    <td class="py-3">
                                        @if($record['is_weekend'])
                                            <span class="px-2 py-1 rounded-full text-xs bg-gray-700 text-gray-300">Akhir Pekan</span>
                                        @elseif($record['status'] == 'hadir')
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Hadir</span>
                                        @elseif($record['status'] == 'sakit')
                                            <span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">Sakit</span>
                                        @elseif($record['status'] == 'izin')
                                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">Izin</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Alpha</span>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ $record['time'] ?? '-' }}</td>
                                    <td class="py-3 max-w-xs truncate">{{ $record['notes'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p>Data pegawai tidak ditemukan</p>
                </div>
                @endif
            </div>
            
            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-700 flex justify-end">
                <button wire:click="closeDetailModal" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        @media print {
            body {
                background-color: white;
                color: black;
            }
            .rounded-3xl {
                border-radius: 0 !important;
            }
            .bg-gray-800\/50 {
                background-color: white !important;
            }
        }
    </style>
</div>