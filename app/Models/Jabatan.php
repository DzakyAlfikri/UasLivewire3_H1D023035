<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    
    protected $fillable = [
        'nama_jabatan',
        'tunjangan'
    ];

    protected $casts = [
        'tunjangan' => 'decimal:2'
    ];

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
}