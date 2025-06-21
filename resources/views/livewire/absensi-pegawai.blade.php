<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Absensi Pegawai</h1>
    
    <!-- Message Notification -->
    @if($showMessage)
    <div class="fixed top-4 right-4 z-50 w-64 bg-{{ $message['type'] == 'success' ? 'green' : 'red' }}-500 text-white p-3 rounded-lg shadow-lg" 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => { show = false }, 3000)"
        wire:key="message-{{ now() }}">
        <div class="flex items-center">
            <i class="fas fa-{{ $message['type'] == 'success' ? 'check-circle' : 'exclamation-circle' }} mr-2"></i>
            <p>{{ $message['text'] }}</p>
        </div>
    </div>
    @endif
    
    @if($pegawai)
        <!-- Today's Attendance Card -->
        <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6">
            <h2 class="text-xl font-semibold text-white mb-6">Absensi Hari Ini</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-700 text-white">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium">{{ $pegawai->nama }}</div>
                                <div class="text-sm text-gray-400">{{ $pegawai->nip }} - {{ $pegawai->jabatan->nama_jabatan ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-700 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-gray-400">Tanggal</div>
                                <div class="font-medium">{{ Carbon\Carbon::now()->format('l, d F Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-400">Waktu</div>
                                <div class="font-medium" id="live-clock">{{ Carbon\Carbon::now()->format('H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($todayAttendance)
                <div class="bg-gray-700/30 rounded-3xl p-6 text-center">
                    <div class="w-20 h-20 mx-auto bg-{{ $todayAttendance->status == 'hadir' ? 'green' : 
                        ($todayAttendance->status == 'tidak_hadir' ? 'red' : 
                        ($todayAttendance->status == 'izin' ? 'yellow' : 'orange')) }}-500/30 
                        rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-{{ $todayAttendance->status == 'hadir' ? 'check' : 
                            ($todayAttendance->status == 'tidak_hadir' ? 'times' : 
                            ($todayAttendance->status == 'izin' ? 'exclamation' : 'thermometer-half')) }} 
                            text-{{ $todayAttendance->status == 'hadir' ? 'green' : 
                            ($todayAttendance->status == 'tidak_hadir' ? 'red' : 
                            ($todayAttendance->status == 'izin' ? 'yellow' : 'orange')) }}-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-white mb-2">
                        Anda sudah melakukan absensi hari ini
                    </h3>
                    <p class="text-gray-400 mb-4">Status: 
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $todayAttendance->status == 'hadir' ? 'bg-green-100 text-green-800' : 
                            ($todayAttendance->status == 'tidak_hadir' ? 'bg-red-100 text-red-800' : 
                            ($todayAttendance->status == 'izin' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $todayAttendance->status)) }}
                        </span>
                    </p>
                    <p class="text-gray-400">Waktu absen: {{ $todayAttendance->created_at->format('H:i:s') }}</p>
                    
                    @if($todayAttendance->keterangan)
                        <div class="mt-4 bg-gray-800/50 rounded-xl p-4 inline-block">
                            <p class="text-gray-300">{{ $todayAttendance->keterangan }}</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="mb-6">
                    <div class="bg-gray-700/30 rounded-xl p-4 mb-4 border border-gray-700">
                        <label for="keterangan" class="block text-sm text-gray-400 mb-2">Keterangan (opsional)</label>
                        <textarea wire:model="keterangan" id="keterangan" rows="2" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-3 px-4 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Berikan keterangan jika diperlukan..."></textarea>
                    </div>
                
                    <div class="flex flex-col md:flex-row gap-4 justify-center">
                        <button wire:click="submitAttendance('hadir')" 
                            class="bg-green-500 hover:bg-green-600 transition-colors duration-200 
                            text-white py-3 px-6 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check mr-2"></i> Hadir
                        </button>
                        
                        <button wire:click="submitAttendance('izin')" 
                            class="bg-yellow-500 hover:bg-yellow-600 transition-colors duration-200 
                            text-white py-3 px-6 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation mr-2"></i> Izin
                        </button>
                        
                        <button wire:click="submitAttendance('sakit')" 
                            class="bg-orange-500 hover:bg-orange-600 transition-colors duration-200 
                            text-white py-3 px-6 rounded-xl flex items-center justify-center">
                            <i class="fas fa-thermometer-half mr-2"></i> Sakit
                        </button>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Monthly Attendance Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-3xl p-6 text-white shadow-lg shadow-green-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold">{{ $stats['hadir'] }}</h2>
                        <p class="text-sm text-green-100">Kehadiran</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-3xl p-6 text-white shadow-lg shadow-yellow-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold">{{ $stats['izin'] + $stats['sakit'] }}</h2>
                        <p class="text-sm text-yellow-100">Izin & Sakit</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-exclamation text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-3xl p-6 text-white shadow-lg shadow-red-500/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold">{{ $stats['alpha'] }}</h2>
                        <p class="text-sm text-red-100">Alpha</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-times text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Monthly Attendance Records -->
        <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Rekap Absensi Bulanan</h2>
                
                <div class="flex items-center">
                    <button wire:click="changeMonth('prev')" class="bg-gray-700/50 hover:bg-gray-700 transition-colors p-2 rounded-l-lg">
                        <i class="fas fa-chevron-left text-gray-400"></i>
                    </button>
                    <div class="bg-gray-700/50 px-4 py-2">
                        {{ Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
                    </div>
                    <button wire:click="changeMonth('next')" class="bg-gray-700/50 hover:bg-gray-700 transition-colors p-2 rounded-r-lg" {{ $bulan == now()->month && $tahun == now()->year ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gray-700/70 flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400">Persentase Kehadiran</div>
                        <div class="text-2xl font-bold text-white">{{ $stats['persentase'] }}%</div>
                    </div>
                </div>
                
                <div class="flex space-x-4">
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
                        @php
                            $startDate = Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
                            $endDate = Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
                            $now = Carbon\Carbon::now();
                            
                            // Only show dates up to today
                            if ($endDate->gt($now)) {
                                $endDate = $now;
                            }
                        @endphp
                        
                        @for($date = $endDate->copy(); $date->gte($startDate); $date->subDay())
                            @php
                                $dateStr = $date->format('Y-m-d');
                                $record = $attendanceRecords[$dateStr] ?? null;
                                $isWeekend = $date->isWeekend();
                            @endphp
                            
                            <tr class="border-b border-gray-800 {{ $isWeekend ? 'bg-gray-800/30' : '' }}">
                                <td class="py-3">{{ $date->format('d/m/Y') }}</td>
                                <td class="py-3 {{ $isWeekend ? 'text-orange-400' : '' }}">{{ $date->translatedFormat('l') }}</td>
                                <td class="py-3">
                                    @if($isWeekend)
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-700 text-gray-300">Akhir Pekan</span>
                                    @elseif($record)
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            {{ $record->status == 'hadir' ? 'bg-green-100 text-green-800' : 
                                            ($record->status == 'tidak_hadir' ? 'bg-red-100 text-red-800' : 
                                            ($record->status == 'izin' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                        </span>
                                    @elseif($date->lt($now))
                                        <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Alpha</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-700 text-gray-300">Belum Absen</span>
                                    @endif
                                </td>
                                <td class="py-3">{{ $record ? $record->created_at->format('H:i:s') : '-' }}</td>
                                <td class="py-3 max-w-xs truncate">{{ $record ? $record->keterangan : '-' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p>Data pegawai tidak ditemukan. Silakan hubungi administrator.</p>
            </div>
        </div>
    @endif
    
    <script>
        // Live clock script
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        
        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
        
        // Auto-hide message after delay
        window.addEventListener('hideMessage', event => {
            setTimeout(() => {
                @this.set('showMessage', false);
            }, 3000);
        });
    </script>
</div>
