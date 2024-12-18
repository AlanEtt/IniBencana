<?php

namespace App\Http\Controllers;

use App\Models\AidType;
use Illuminate\Http\Request;

class AidTypeController extends Controller
{
    public function index()
    {
        $aidTypes = AidType::all();
        return view('aid_types.index', compact('aidTypes'));
    }

    public function create()
    {
        return view('aid_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        AidType::create($request->all());
        return redirect()->route('aid-types.index')->with('success', 'Tipe bantuan berhasil ditambahkan.');
    }

    public function show(AidType $aidType)
    {
        return view('aid_types.show', compact('aidType'));
    }

    public function edit(AidType $aidType)
    {
        return view('aid_types.edit', compact('aidType'));
    }

    public function update(Request $request, AidType $aidType)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $aidType->update($request->all());
        return redirect()->route('aid-types.index')->with('success', 'Tipe bantuan berhasil diperbarui.');
    }

    public function destroy(AidType $aidType)
    {
        $aidType->delete();
        return redirect()->route('aid-types.index')->with('success', 'Tipe bantuan berhasil dihapus.');
    }
}
