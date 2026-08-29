<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'product_type'];

    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    public function scopeForProductType($query, ?string $type)
    {
        if (empty($type) || $type === 'all') {
            return $query;
        }

        return $query->where(function ($q) use ($type) {
            $q->where('product_type', $type)
              ->orWhere('product_type', 'both')
              ->orWhereNull('product_type');
        });
    }
}