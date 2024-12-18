<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'location',
        'description',
        'date',
        'severity',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'date' => 'datetime',
        'severity' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function aidDistributions()
    {
        return $this->hasMany(AidDistribution::class);
    }
}
