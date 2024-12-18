<?php

namespace App\Http\Controllers;

use App\Models\DisasterLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisasterLocationController extends Controller
{
    public function index()
    {
        $disasters = DisasterLocation::all();
        return view('disasters.index', compact('disasters'));
    }

    public function create()
    {
        return view('disasters.create');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'type' => 'required',
                'location' => 'required',
                'description' => 'nullable',
                'date' => 'required|date',
                'severity' => 'required|integer|between:1,10',
            ]);

            Log::info('Data yang diterima:', $request->all());

            // Ambil koordinat dari input location
            $coordinates = $request->location;
            list($lat, $lng) = explode(',', $coordinates);

            Log::info('Koordinat yang diproses:', ['lat' => $lat, 'lng' => $lng]);

            // Simpan ke database
            $disaster = DisasterLocation::create([
                'type' => $request->type,
                'location' => $request->location,
                'description' => $request->description,
                'date' => $request->date,
                'severity' => $request->severity,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            Log::info('Data berhasil disimpan');

            return redirect()
                ->route('disasters.index')
                ->with('success', 'Bencana berhasil ditambahkan.');

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

    public function show(DisasterLocation $disaster)
    {
        return view('disasters.show', compact('disaster'));
    }

    public function edit(DisasterLocation $disaster)
    {
        return view('disasters.edit', compact('disaster'));
    }

    public function update(Request $request, DisasterLocation $disaster)
    {
        try {
            $validated = $request->validate([
                'type' => 'required',
                'location' => 'required',
                'description' => 'nullable',
                'date' => 'required|date',
                'severity' => 'required|integer|between:1,10',
            ]);

            $coordinates = $request->location;
            list($lat, $lng) = explode(',', $coordinates);

            $disaster->update([
                'type' => $request->type,
                'location' => $request->location,
                'description' => $request->description,
                'date' => $request->date,
                'severity' => $request->severity,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            return redirect()
                ->route('disasters.index')
                ->with('success', 'Bencana berhasil diperbarui.');

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

    public function destroy(DisasterLocation $disaster)
    {
        try {
            $disaster->delete();
            return redirect()
                ->route('disasters.index')
                ->with('success', 'Bencana berhasil dihapus.');
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
