<?php

namespace App\Http\Controllers;

use App\Models\ShelterLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShelterLocationController extends Controller
{
    public function index()
    {
        $shelters = ShelterLocation::all();
        return view('shelters.index', compact('shelters'));
    }

    public function create()
    {
        return view('shelters.create');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required',
                'capacity' => 'required|integer',
                'location' => 'required',
                'facilities' => 'required',
            ]);

            Log::info('Data yang diterima:', $request->all());

            // Ambil koordinat dari input location
            $coordinates = $request->location;
            list($lat, $lng) = explode(',', $coordinates);

            Log::info('Koordinat yang diproses:', ['lat' => $lat, 'lng' => $lng]);

            // Simpan ke database
            $shelter = ShelterLocation::create([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'facilities' => $request->facilities,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            Log::info('Data berhasil disimpan');

            return redirect()
                ->route('shelters.index')
                ->with('success', 'Lokasi penampungan berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(ShelterLocation $shelter)
    {
        return view('shelters.show', compact('shelter'));
    }

    public function edit(ShelterLocation $shelter)
    {
        return view('shelters.edit', compact('shelter'));
    }

    public function update(Request $request, ShelterLocation $shelter)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required',
                'capacity' => 'required|integer',
                'location' => 'required',
                'facilities' => 'required',
            ]);

            // Ambil koordinat dari input location
            $coordinates = $request->location;
            list($lat, $lng) = explode(',', $coordinates);

            // Update data
            $shelter->update([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'facilities' => $request->facilities,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            return redirect()
                ->route('shelters.index')
                ->with('success', 'Lokasi penampungan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error saat update data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(ShelterLocation $shelter)
    {
        try {
            $shelter->delete();
            return redirect()
                ->route('shelters.index')
                ->with('success', 'Lokasi penampungan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
