@extends('layouts.app')

@section('content')
<div>
    <h1 class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600 mb-6">Dashboard</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/20">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold">{{ App\Models\Pegawai::count() }}</h2>
                    <p class="text-sm text-blue-100">Total Pegawai</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-3xl p-6 text-white shadow-lg shadow-green-500/20">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold">{{ App\Models\Jabatan::count() }}</h2>
                    <p class="text-sm text-green-100">Total Jabatan</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-3xl p-6 text-white shadow-lg shadow-purple-500/20">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold">{{ App\Models\UnitKerja::count() }}</h2>
                    <p class="text-sm text-purple-100">Unit Kerja</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-building text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-lg shadow-amber-500/20">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold">{{ App\Models\Cuti::where('status', 'pending')->count() }}</h2>
                    <p class="text-sm text-amber-100">Cuti Pending</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pengajuan Cuti Chart -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Pengajuan Cuti Terbaru</h2>
                    <a href="{{ route('cuti') }}" class="text-orange-500 text-sm hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 border-b border-gray-700">
                                <th class="pb-3">Pegawai</th>
                                <th class="pb-3">Tanggal Mulai</th>
                                <th class="pb-3">Tanggal Akhir</th>
                                <th class="pb-3">Durasi</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(App\Models\Cuti::with('pegawai')->latest()->take(5)->get() as $cuti)
                            <tr class="border-b border-gray-800">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <span>{{ $cuti->pegawai->nama }}</span>
                                    </div>
                                </td>
                                <td class="py-4">{{ $cuti->tanggal_mulai->format('d/m/Y') }}</td>
                                <td class="py-4">{{ $cuti->tanggal_akhir->format('d/m/Y') }}</td>
                                <td class="py-4">{{ $cuti->durasi }} hari</td>
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
            </div>
        </div>

        <!-- Activity Card -->
        <div>
            <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 h-full">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Aktivitas Hari Ini</h2>
                    <a href="{{ route('absensi') }}" class="text-orange-500 text-sm hover:underline">Lihat Absensi</a>
                </div>
                
                @php
                $todayAbsensi = App\Models\Absensi::whereDate('tanggal', today())
                                ->with('pegawai')
                                ->latest()
                                ->take(4)
                                ->get();
                @endphp
                
                @if($todayAbsensi->count() > 0)
                    <div class="space-y-4">
                        @foreach($todayAbsensi as $absensi)
                            <div class="flex items-center space-x-3 bg-gray-700/30 rounded-xl p-3">
                                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-100">{{ $absensi->pegawai->nama }}</h4>
                                    <p class="text-xs text-gray-500">{{ $absensi->pegawai->jabatan->nama_jabatan ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $absensi->status == 'hadir' ? 'bg-green-100 text-green-800' : 
                                          ($absensi->status == 'tidak_hadir' ? 'bg-red-100 text-red-800' : 
                                          ($absensi->status == 'izin' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $absensi->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500">
                        <i class="fas fa-calendar-day text-4xl mb-3"></i>
                        <p>Belum ada data absensi hari ini</p>
                    </div>
                @endif
                
                <!-- Promo Card -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-3xl p-6 mt-6 text-white shadow-lg">
                    <h3 class="text-xl font-semibold mb-2">Kelola Absensi</h3>
                    <p class="text-sm opacity-90 mb-4">Pantau kehadiran pegawai dan buat laporan dengan mudah!</p>
                    <a href="{{ route('absensi') }}" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-xl text-sm 
                                hover:bg-white/30 transition-all duration-200 flex items-center space-x-2 w-max">
                        <span>Atur Absensi</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection