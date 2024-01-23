<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::with('allChildren')->whereNull('parent_id')->get();
        
        return Inertia::render('Units/Index', [
            'units' => $units,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email'       => ['required', 'email', 'max:255', 'unique:units'],
            'phone'       => ['required', 'string', 'max:255'],
            'latitude'    => ['required', 'numeric'],
            'longitude'   => ['required', 'numeric'],
        ]);
        Unit::create([
            'name'        => $request->name,
            'description' => $request->description,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return redirect()->route('dashboard')->with('success', 'Unidade criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email'       => ['required', 'email', 'max:255', 'unique:units'],
            'phone'       => ['required', 'string', 'max:255'],
            'latitude'    => ['required', 'numeric'],
            'longitude'   => ['required', 'numeric'],
        ]);
        $unit = Unit::findOrFail($id);
        $unit->update($request->all());

        return redirect()->route('dashboard')->with('success', 'Unidade atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->route('dashboard')->with('success', 'Unidade excluída com sucesso.');
    }
}
