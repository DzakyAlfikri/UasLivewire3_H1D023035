<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Informasi Gaji</h1>
    
    @if($pegawai)
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
                    <p class="text-lg font-medium">ID Pegawai: {{ $pegawai->id }}</p>
                    <p class="text-blue-100">Tgl. Bergabung: {{ \Carbon\Carbon::parse($pegawai->created_at)->format('d F Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Period Selector -->
        <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-white mb-4 md:mb-0">Periode Gaji</h2>
                
                <div class="flex flex-col md:flex-row gap-4">
                    <div>
                        <label for="selectedMonth" class="block text-sm text-gray-400 mb-1">Bulan</label>
                        <select wire:model.live="selectedMonth" id="selectedMonth" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            @foreach($months as $monthNum => $monthName)
                                <option value="{{ $monthNum }}">{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="selectedYear" class="block text-sm text-gray-400 mb-1">Tahun</label>
                        <select wire:model.live="selectedYear" id="selectedYear" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Salary Slip Card -->
        <div class="bg-gray-800/50 rounded-3xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Slip Gaji</h2>
                        <p class="text-orange-100">Periode: {{ $salaryDetail['period'] }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-money-bill-wave text-2xl text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Salary Detail -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Pendapatan</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Gaji Pokok</span>
                                <span class="text-white font-medium">Rp {{ number_format($salaryDetail['basic_salary'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Tunjangan Jabatan</span>
                                <span class="text-white font-medium">Rp {{ number_format($salaryDetail['allowance'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400 font-medium">Total Pendapatan</span>
                                <span class="text-green-400 font-semibold">Rp {{ number_format($salaryDetail['total_salary'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Potongan</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <div>
                                    <span class="text-gray-400">Ketidakhadiran</span>
                                    <span class="text-gray-500 text-sm block">({{ $salaryDetail['absences'] }} hari × Rp {{ number_format($salaryDetail['deduction_per_day'], 0, ',', '.') }})</span>
                                </div>
                                <span class="text-red-400 font-medium">Rp {{ number_format($salaryDetail['total_deduction'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400 font-medium">Total Potongan</span>
                                <span class="text-red-400 font-semibold">Rp {{ number_format($salaryDetail['total_deduction'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Summary -->
                <div class="bg-gray-700/30 rounded-2xl p-6 mb-6">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">Total Gaji Bersih</h3>
                            <p class="text-gray-400">Setelah potongan</p>
                        </div>
                        <div class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-green-600 mt-4 md:mt-0">
                            Rp {{ number_format($salaryDetail['final_salary'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Payment Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 bg-gray-800/50 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Tanggal Pembayaran</h4>
                                <p class="text-white">{{ \Carbon\Carbon::parse($salaryDetail['payment_date'])->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 bg-gray-800/50 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Hari Kerja</h4>
                                <p class="text-white">{{ $salaryDetail['workdays'] }} Hari</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 bg-gray-800/50 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-clock text-gray-400"></i>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Ketidakhadiran</h4>
                                <p class="text-white">{{ $salaryDetail['absences'] }} Hari</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Salary History -->
        <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mt-6">
            <h2 class="text-xl font-semibold text-white mb-6">Riwayat Gaji</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-700">
                            <th class="pb-3">Periode</th>
                            <th class="pb-3">Gaji Pokok</th>
                            <th class="pb-3">Tunjangan</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Generate sample salary history for the last 6 months
                            $sampleHistory = [];
                            $now = \Carbon\Carbon::now();
                            
                            for ($i = 0; $i < 6; $i++) {
                                $date = $now->copy()->subMonths($i);
                                $sampleHistory[] = [
                                    'period' => $date->format('F Y'),
                                    'basic' => $pegawai->gaji,
                                    'allowance' => $pegawai->jabatan->tunjangan ?? 0,
                                    'deduction' => rand(0, 5) * ($pegawai->gaji * 0.01),
                                    'total' => $pegawai->gaji_total - (rand(0, 5) * ($pegawai->gaji * 0.01))
                                ];
                            }
                        @endphp
                        
                        @foreach($sampleHistory as $history)
                            <tr class="border-b border-gray-800">
                                <td class="py-4">{{ $history['period'] }}</td>
                                <td class="py-4">Rp {{ number_format($history['basic'], 0, ',', '.') }}</td>
                                <td class="py-4">Rp {{ number_format($history['allowance'], 0, ',', '.') }}</td>
                                <td class="py-4">
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        Dibayar
                                    </span>
                                </td>
                                <td class="py-4 font-medium">Rp {{ number_format($history['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
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
</div>
