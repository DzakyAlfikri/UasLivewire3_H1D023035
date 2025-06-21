<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Cuti extends Model
{
    protected $table = 'cuti';
    
    protected $fillable = [
        'pegawai_id',
        'tanggal_mulai',
        'tanggal_akhir',
        'alasan',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date'
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getDurasiAttribute()
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_akhir) + 1;
    }
}