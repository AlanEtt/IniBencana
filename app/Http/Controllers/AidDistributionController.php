<?php

namespace App\Http\Controllers;

use App\Models\AidDistribution;
use App\Models\DisasterLocation;
use App\Models\ShelterLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AidDistributionController extends Controller
{
    public function index()
    {
        $distributions = AidDistribution::with(['disasterLocation', 'shelterLocation'])->get();
        $disasters = DisasterLocation::all();
        $shelters = ShelterLocation::all();

        return view('aid_distributions.index', compact('distributions', 'disasters', 'shelters'));
    }

    public function create()
    {
        $disasters = DisasterLocation::all();
        $shelters = ShelterLocation::all();
        return view('aid_distributions.create', compact('disasters', 'shelters'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'disaster_location_id' => 'required|exists:disaster_locations,id',
                'shelter_location_id' => 'required|exists:shelter_locations,id',
                'aid_type' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);

            $distribution = AidDistribution::create($validated);

            return redirect()
                ->route('aid-distributions.index')
                ->with('success', 'Data distribusi bantuan berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan distribusi bantuan:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(AidDistribution $aidDistribution)
    {
        $aidDistribution->load(['disasterLocation', 'shelterLocation']);
        return view('aid_distributions.show', compact('aidDistribution'));
    }

    public function edit(AidDistribution $aidDistribution)
    {
        $disasters = DisasterLocation::all();
        $shelters = ShelterLocation::all();
        return view('aid_distributions.edit', compact('aidDistribution', 'disasters', 'shelters'));
    }

    public function update(Request $request, AidDistribution $aidDistribution)
    {
        try {
            $validated = $request->validate([
                'disaster_location_id' => 'required|exists:disaster_locations,id',
                'shelter_location_id' => 'required|exists:shelter_locations,id',
                'aid_type' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);

            $aidDistribution->update($validated);

            return redirect()
                ->route('aid-distributions.index')
                ->with('success', 'Data distribusi bantuan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error saat memperbarui distribusi bantuan:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(AidDistribution $aidDistribution)
    {
        try {
            $aidDistribution->delete();
            return redirect()
                ->route('aid-distributions.index')
                ->with('success', 'Data distribusi bantuan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus distribusi bantuan:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
