<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600 mb-6">Laporan Rekapitulasi Absensi</h1>
    
    <div class="flex justify-between items-center mb-6">
        <div class="text-lg text-gray-300">
            <span>Periode: <strong>{{ $bulanArray[$bulan] }} {{ $tahun }}</strong></span>
        </div>
        <button type="button" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-blue-500/20 transition-all flex items-center space-x-2" onclick="window.print()">
            <i class="fas fa-print"></i>
            <span>Cetak Laporan</span>
        </button>
    </div>

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6 print:hidden">
        <h3 class="text-lg font-medium text-white mb-4">Filter Data Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Bulan</label>
                <select class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" wire:model.live="bulan">
                    @foreach($bulanArray as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Tahun</label>
                <select class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" wire:model.live="tahun">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Pegawai (Opsional)</label>
                <select class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" wire:model.live="pegawai_id">
                    <option value="">Semua Pegawai</option>
                    @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-blue-500/20 transition-all flex items-center justify-center space-x-2" wire:click="$refresh">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 print:shadow-none print:border-0">
        <div class="print:flex print:flex-col print:items-center hidden">
            <h2 class="text-xl font-bold mb-1">Laporan Rekapitulasi Absensi</h2>
            <p class="text-lg mb-4">Periode: {{ $bulanArray[$bulan] }} {{ $tahun }}</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full print:border print:border-collapse">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-700 print:bg-gray-100 print:text-gray-800">
                        <th class="pb-3 px-2 print:border print:p-2">No</th>
                        <th class="pb-3 px-2 print:border print:p-2">NIP</th>
                        <th class="pb-3 px-2 print:border print:p-2">Nama Pegawai</th>
                        <th class="pb-3 px-2 print:border print:p-2">Jabatan</th>
                        <th class="pb-3 px-2 print:border print:p-2">Unit Kerja</th>
                        <th class="pb-3 px-2 print:border print:p-2 text-center">Hadir</th>
                        <th class="pb-3 px-2 print:border print:p-2 text-center">Tidak Hadir</th>
                        <th class="pb-3 px-2 print:border print:p-2 text-center">Sakit</th>
                        <th class="pb-3 px-2 print:border print:p-2 text-center">Izin</th>
                        <th class="pb-3 px-2 print:border print:p-2 text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($rekapitulasi->count() > 0)
                        @foreach($rekapitulasi as $index => $rekap)
                        <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition-colors print:hover:bg-transparent print:text-black print:border">
                            <td class="py-4 px-2 print:border print:p-2">{{ $index + 1 }}</td>
                            <td class="py-4 px-2 print:border print:p-2">{{ $rekap['pegawai']->nip }}</td>
                            <td class="py-4 px-2 print:border print:p-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mr-3 print:hidden">
                                        <span class="text-white font-medium">{{ strtoupper(substr($rekap['pegawai']->nama, 0, 1)) }}</span>
                                    </div>
                                    <span>{{ $rekap['pegawai']->nama }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-2 print:border print:p-2">{{ $rekap['pegawai']->jabatan->nama_jabatan }}</td>
                            <td class="py-4 px-2 print:border print:p-2">{{ $rekap['pegawai']->unitKerja->nama_unit }}</td>
                            <td class="py-4 px-2 print:border print:p-2 text-center">
                                <span class="bg-green-900/20 text-green-500 py-1 px-2.5 rounded-lg font-medium print:bg-transparent print:text-black print:p-0">
                                    {{ $rekap['hadir'] }}
                                </span>
                            </td>
                            <td class="py-4 px-2 print:border print:p-2 text-center">
                                <span class="bg-red-900/20 text-red-500 py-1 px-2.5 rounded-lg font-medium print:bg-transparent print:text-black print:p-0">
                                    {{ $rekap['tidak_hadir'] }}
                                </span>
                            </td>
                            <td class="py-4 px-2 print:border print:p-2 text-center">
                                <span class="bg-yellow-900/20 text-yellow-500 py-1 px-2.5 rounded-lg font-medium print:bg-transparent print:text-black print:p-0">
                                    {{ $rekap['sakit'] }}
                                </span>
                            </td>
                            <td class="py-4 px-2 print:border print:p-2 text-center">
                                <span class="bg-blue-900/20 text-blue-500 py-1 px-2.5 rounded-lg font-medium print:bg-transparent print:text-black print:p-0">
                                    {{ $rekap['izin'] }}
                                </span>
                            </td>
                            <td class="py-4 px-2 print:border print:p-2 text-center font-bold">
                                <span class="bg-gray-700/50 text-white py-1 px-2.5 rounded-lg print:bg-transparent print:text-black print:p-0">
                                    {{ $rekap['total'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="py-4 px-2 text-center text-gray-400">Tidak ada data absensi untuk periode ini</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-900/50 print:bg-gray-200 print:text-black">
                        <td colspan="5" class="py-3 px-2 print:border print:p-2 text-right">Total:</td>
                        <td class="py-3 px-2 print:border print:p-2 text-center text-green-400 print:text-green-800">{{ $rekapitulasi->sum('hadir') }}</td>
                        <td class="py-3 px-2 print:border print:p-2 text-center text-red-400 print:text-red-800">{{ $rekapitulasi->sum('tidak_hadir') }}</td>
                        <td class="py-3 px-2 print:border print:p-2 text-center text-yellow-400 print:text-yellow-800">{{ $rekapitulasi->sum('sakit') }}</td>
                        <td class="py-3 px-2 print:border print:p-2 text-center text-blue-400 print:text-blue-800">{{ $rekapitulasi->sum('izin') }}</td>
                        <td class="py-3 px-2 print:border print:p-2 text-center text-white print:text-black">{{ $rekapitulasi->sum('total') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="mt-8 print:block hidden">
            <div class="flex justify-end">
                <div class="text-center">
                    <p>Jakarta, {{ now()->format('d M Y') }}</p>
                    <p class="mb-12">Manajer HRD</p>
                    <p class="font-bold underline">Nama Manajer HRD</p>
                    <p>NIP. 123456789</p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            @page {
                size: landscape;
            }
            
            body {
                font-family: 'Arial', sans-serif;
                color: black;
                background: white;
            }
            
            .print\:hidden {
                display: none !important;
            }
            
            .print\:block {
                display: block !important;
            }
            
            .print\:flex {
                display: flex !important;
            }
            
            .print\:border {
                border: 1px solid #ddd !important;
            }
            
            .print\:p-2 {
                padding: 0.5rem !important;
            }
            
            .print\:shadow-none {
                box-shadow: none !important;
            }
            
            .print\:border-0 {
                border: none !important;
            }
            
            .print\:bg-gray-100 {
                background-color: #f3f4f6 !important;
            }
            
            .print\:bg-gray-200 {
                background-color: #e5e7eb !important;
            }
            
            .print\:text-black {
                color: black !important;
            }
            
            .print\:text-gray-800 {
                color: #1f2937 !important;
            }
            
            .print\:hover\:bg-transparent:hover {
                background-color: transparent !important;
            }
            
            .print\:bg-transparent {
                background-color: transparent !important;
            }
            
            .print\:p-0 {
                padding: 0 !important;
            }
            
            .print\:border-collapse {
                border-collapse: collapse !important;
            }
            
            .print\:text-green-800 {
                color: #166534 !important;
            }
            
            .print\:text-red-800 {
                color: #991b1b !important;
            }
            
            .print\:text-yellow-800 {
                color: #854d0e !important;
            }
            
            .print\:text-blue-800 {
                color: #1e40af !important;
            }
        }
    </style>
</div>