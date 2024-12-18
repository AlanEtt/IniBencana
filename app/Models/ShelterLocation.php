<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShelterLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'facilities',
        'latitude',
        'longitude'
    ];

    // Accessor untuk mendapatkan lokasi dalam format lat,lng
    public function getLocationAttribute()
    {
        return $this->latitude . ',' . $this->longitude;
    }

    public function aidDistributions()
    {
        return $this->hasMany(AidDistribution::class);
    }
}
