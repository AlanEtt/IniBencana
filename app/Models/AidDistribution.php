<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AidDistribution extends Model
{
    use HasFactory;

    protected $fillable = ['disaster_id', 'shelter_id', 'aid_type_id', 'quantity', 'date'];

    public function disasterLocation()
    {
        return $this->belongsTo(DisasterLocation::class);
    }

    public function shelterLocation()
    {
        return $this->belongsTo(ShelterLocation::class);
    }

    public function aidType()
    {
        return $this->belongsTo(AidType::class);
    }
}
