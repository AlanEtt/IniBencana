<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AidType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',          // Kategori bantuan (makanan, pakaian, obat-obatan, dll)
        'unit',             // Satuan bantuan (kg, pcs, box, dll)
        'priority_level',   // Tingkat prioritas bantuan (tinggi, sedang, rendah)
        'is_perishable',    // Apakah bantuan mudah rusak/kadaluarsa
        'storage_method',   // Cara penyimpanan bantuan
        'distribution_method', // Metode pendistribusian bantuan
        'donor_name',       // Nama penyumbang/donatur
        'donor_contact',    // Kontak penyumbang
        'donor_type',       // Tipe penyumbang (individu/organisasi)
        'donation_date'     // Tanggal donasi diterima
    ];

    protected $casts = [
        'is_perishable' => 'boolean',
        'donation_date' => 'datetime'
    ];

    // Relasi dengan distribusi bantuan
    public function aidDistributions()
    {
        return $this->hasMany(AidDistribution::class);
    }

    // Scope untuk filter berdasarkan kategori
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope untuk filter berdasarkan prioritas
    public function scopePriority($query, $level)
    {
        return $query->where('priority_level', $level);
    }

    // Scope untuk filter berdasarkan penyumbang
    public function scopeDonor($query, $donorName)
    {
        return $query->where('donor_name', 'like', "%{$donorName}%");
    }

    // Scope untuk filter berdasarkan tanggal donasi
    public function scopeDonationDate($query, $date)
    {
        return $query->whereDate('donation_date', $date);
    }
}
