<?php

namespace App\Http\Controllers;

use App\Models\AidDistribution;
use App\Models\DisasterLocation;
use App\Models\ShelterLocation;
use App\Models\AidType;
use Illuminate\Http\Request;

class AidDistributionController extends Controller
{
    public function index()
    {
        $distributions = AidDistribution::with(['disasterLocation', 'shelterLocation', 'aidType'])->get();
        return view('aid_distributions.index', compact('distributions'));
    }

    public function create()
    {
        $disasters = DisasterLocation::all();
        $shelters = ShelterLocation::all();
        $aidTypes = AidType::all();
        return view('aid_distributions.create', compact('disasters', 'shelters', 'aidTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'disaster_id' => 'required|exists:disaster_locations,id',
            'shelter_id' => 'required|exists:shelter_locations,id',
            'aid_type_id' => 'required|exists:aid_types,id',
            'quantity' => 'required|integer',
            'date' => 'required|date',
        ]);

        AidDistribution::create($request->all());
        return redirect()->route('aid-distributions.index')->with('success', 'Distribusi bantuan berhasil ditambahkan.');
    }

    public function show(AidDistribution $aidDistribution)
    {
        return view('aid_distributions.show', compact('aidDistribution'));
    }

    public function edit(AidDistribution $aidDistribution)
    {
        $disasters = DisasterLocation::all();
        $shelters = ShelterLocation::all();
        $aidTypes = AidType::all();
        return view('aid_distributions.edit', compact('aidDistribution', 'disasters', 'shelters', 'aidTypes'));
    }

    public function update(Request $request, AidDistribution $aidDistribution)
    {
        $request->validate([
            'disaster_id' => 'required|exists:disaster_locations,id',
            'shelter_id' => 'required|exists:shelter_locations,id',
            'aid_type_id' => 'required|exists:aid_types,id',
            'quantity' => 'required|integer',
            'date' => 'required|date',
        ]);

        $aidDistribution->update($request->all());
        return redirect()->route('aid-distributions.index')->with('success', 'Distribusi bantuan berhasil diperbarui.');
    }

    public function destroy(AidDistribution $aidDistribution)
    {
        $aidDistribution->delete();
        return redirect()->route('aid-distributions.index')->with('success', 'Distribusi bantuan berhasil dihapus.');
    }
}
