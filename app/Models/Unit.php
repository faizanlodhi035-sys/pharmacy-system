<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'unit_id',
        'name',
        'symbol',
        'allow_decimal',
        'status',
    ];

    protected $casts = [
        'allow_decimal' => 'boolean',
    ];

    public function packagings(): HasMany
    {
        return $this->hasMany(MedicinePackaging::class, 'unit_id');
    }

    public function medicinesWithBase(): HasMany
    {
        return $this->hasMany(Medicine::class, 'base_unit_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
