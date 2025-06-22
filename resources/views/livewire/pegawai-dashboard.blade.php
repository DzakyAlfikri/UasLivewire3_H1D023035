<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Dashboard Pegawai</h1>
    
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
    
    <!-- Employee Info Card -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/20 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ Auth::user()->name }}</h2>
                    <p class="text-blue-100">{{ $pegawai->nip ?? 'NIP tidak tersedia' }}</p>
                    <p class="text-blue-100">{{ $pegawai->jabatan->nama_jabatan ?? 'Jabatan tidak tersedia' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-medium">{{ now()->format('l, d F Y') }}</p>
                <p class="text-blue-100" id="live-clock">{{ now()->format('H:i:s') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Attendance Section -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6">
                <h2 class="text-xl font-semibold text-white mb-6">Absensi Hari Ini</h2>
                
                @if($todayAttendance)
                    <div class="bg-gray-700/30 rounded-3xl p-6 text-center">
                        <div class="w-20 h-20 mx-auto bg-{{ $todayAttendance->status == 'hadir' ? 'green' : 
                            ($todayAttendance->status == 'tidak_hadir' ? 'red' : 'yellow') }}-500/30 
                            rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-{{ $todayAttendance->status == 'hadir' ? 'check' : 
                                ($todayAttendance->status == 'tidak_hadir' ? 'times' : 'exclamation') }} 
                                text-{{ $todayAttendance->status == 'hadir' ? 'green' : 
                                ($todayAttendance->status == 'tidak_hadir' ? 'red' : 'yellow') }}-500 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-white mb-2">
                            Anda sudah melakukan absensi hari ini
                        </h3>
                        <p class="text-gray-400 mb-4">Status: 
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $todayAttendance->status == 'hadir' ? 'bg-green-100 text-green-800' : 
                                ($todayAttendance->status == 'tidak_hadir' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $todayAttendance->status)) }}
                            </span>
                        </p>
                        <p class="text-gray-400">Waktu absen: {{ $todayAttendance->created_at->format('H:i:s') }}</p>
                    </div>
                @else
                    <div class="bg-gray-700/30 rounded-3xl p-6">
                        <div class="mb-6 text-center">
                            <p class="text-lg text-white mb-2">Silakan lakukan absensi untuk hari ini</p>
                            <p class="text-sm text-gray-400">{{ now()->format('l, d F Y') }}</p>
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
            
            <!-- Recent Attendance History -->
            <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Riwayat Absensi</h2>
                </div>
                
                @if($recentAttendance && $recentAttendance->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-700">
                                    <th class="pb-3">Tanggal</th>
                                    <th class="pb-3">Hari</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Waktu Absen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $attendance)
                                <tr class="border-b border-gray-800">
                                    <td class="py-4">{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-4">{{ \Carbon\Carbon::parse($attendance->tanggal)->locale('id')->dayName }}</td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            {{ $attendance->status == 'hadir' ? 'bg-green-100 text-green-800' : 
                                            ($attendance->status == 'tidak_hadir' ? 'bg-red-100 text-red-800' : 
                                            ($attendance->status == 'izin' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4">{{ $attendance->created_at->format('H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500">
                        <i class="fas fa-calendar-day text-4xl mb-3"></i>
                        <p>Belum ada data absensi</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Leave Request Card -->
            <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Pengajuan Cuti</h2>
                    <a href="{{ route('cuti.create') }}" class="text-orange-500 text-sm hover:underline">Ajukan Cuti</a>
                </div>
                
                @if($cutiList && $cutiList->count() > 0)
                    <div class="space-y-4">
                        @foreach($cutiList as $cuti)
                            <div class="bg-gray-700/30 rounded-xl p-4">
                                <div class="flex justify-between mb-2">
                                    <h4 class="font-medium text-white">
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M') }} - 
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_akhir)->format('d M Y') }}
                                    </h4>
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        {{ $cuti->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                          ($cuti->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($cuti->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400">{{ $cuti->durasi }} hari</p>
                                <p class="text-sm text-gray-400 truncate">{{ $cuti->keterangan }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-calendar-alt text-3xl mb-2"></i>
                        <p>Belum ada pengajuan cuti</p>
                    </div>
                @endif
                
                <div class="mt-4">
                    <a href="{{ route('cuti.create') }}" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white w-full py-3 rounded-xl text-center block hover:from-orange-600 hover:to-orange-700 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Ajukan Cuti Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alpine.js is required for some animations -->
    <script>
        // Live clock script
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;
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
