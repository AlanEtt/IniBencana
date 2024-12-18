<?php

namespace App\Http\Controllers;

use App\Models\AidType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AidTypeController extends Controller
{
    public function index()
    {
        $aidTypes = AidType::latest()->get();
        return view('aid_types.index', compact('aidTypes'));
    }

    public function create()
    {
        $categories = ['Makanan', 'Pakaian', 'Obat-obatan', 'Peralatan', 'Lainnya'];
        $units = ['kg', 'pcs', 'box', 'liter', 'paket'];
        return view('aid_types.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'priority_level' => 'required|in:tinggi,sedang,rendah',
            'is_perishable' => 'required|boolean',
            'storage_method' => 'nullable|string|max:255',
            'distribution_method' => 'nullable|string|max:255',
            'donor_name' => 'required|string|max:255',
            'donor_contact' => 'nullable|string|max:255',
            'donor_type' => 'required|in:individu,organisasi',
            'donation_date' => 'required|date'
        ]);

        AidType::create($request->all());
        return redirect()->route('aid-types.index')->with('success', 'Jenis bantuan berhasil ditambahkan.');
    }

    public function show(AidType $aidType)
    {
        return view('aid_types.show', compact('aidType'));
    }

    public function edit(AidType $aidType)
    {
        $categories = ['Makanan', 'Pakaian', 'Obat-obatan', 'Peralatan', 'Lainnya'];
        $units = ['kg', 'pcs', 'box', 'liter', 'paket'];
        return view('aid_types.edit', compact('aidType', 'categories', 'units'));
    }

    public function update(Request $request, AidType $aidType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'priority_level' => 'required|in:tinggi,sedang,rendah',
            'is_perishable' => 'required|boolean',
            'storage_method' => 'nullable|string|max:255',
            'distribution_method' => 'nullable|string|max:255',
            'donor_name' => 'required|string|max:255',
            'donor_contact' => 'nullable|string|max:255',
            'donor_type' => 'required|in:individu,organisasi',
            'donation_date' => 'required|date'
        ]);

        $aidType->update($request->all());
        return redirect()->route('aid-types.index')->with('success', 'Jenis bantuan berhasil diperbarui.');
    }

    public function destroy(AidType $aidType)
    {
        $aidType->delete();
        return redirect()->route('aid-types.index')->with('success', 'Jenis bantuan berhasil dihapus.');
    }
}
