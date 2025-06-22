<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use SoftDeletes;
    
    protected $table = 'pegawai';
    
    protected $fillable = [
        'nip',
        'nama',
        'jabatan_id',
        'unit_kerja_id',
        'gaji'
    ];

    protected $casts = [
        'gaji' => 'decimal:2'
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function cuti(): HasMany
    {
        return $this->hasMany(Cuti::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGajiTotalAttribute()
    {
        return $this->gaji + $this->jabatan->tunjangan;
    }

    public function getCutiTerpakai($tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        
        return $this->cuti()
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $tahun)
            ->get()
            ->sum(function($cuti) {
                return $cuti->tanggal_mulai->diffInDays($cuti->tanggal_akhir) + 1;
            });
    }
}