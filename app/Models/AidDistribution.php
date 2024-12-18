<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AidDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'disaster_location_id',
        'shelter_location_id',
        'aid_type',
        'quantity',
        'description',
        'date'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function disasterLocation()
    {
        return $this->belongsTo(DisasterLocation::class);
    }

    public function shelterLocation()
    {
        return $this->belongsTo(ShelterLocation::class);
    }
}
