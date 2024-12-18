<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AidType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function aidDistributions()
    {
        return $this->hasMany(AidDistribution::class);
    }
}
