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
        $unitsWithChildren = Unit::with('allChildren')->whereNull('parent_id')->get();
        $units             = Unit::all();
        
        return Inertia::render('Units/Index', [
            'units'             => $units,
            'unitsWithChildren' => $unitsWithChildren,
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
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email'       => ['required', 'email', 'max:255', 'unique:units'],
            'phone'       => ['required', 'string', 'max:255'],
            'latitude'    => ['required', 'numeric'],
            'longitude'   => ['required', 'numeric'],
            'parent_id'   => 'nullable|exists:units,id',
        ];

        if (Unit::exists()) {
            $rules['parent_id'] = 'required|exists:units,id';
        }

        $request->validate($rules);

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ];

        if ($request->has('parent_id')) {
            $data['parent_id'] = $request->parent_id;
        }

        Unit::create($data);

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

    public function updateHierarchy(Request $request)
    {
        foreach ($request->units as $update) {
            if (!isset($update['unit_id'])) {
                return redirect()->back()->with('error', 'Dados inválidos recebidos.');
            }
    
            $unit = Unit::findOrFail($update['unit_id']);
            $unit->update(['parent_id' => $update['parent_id'] ?? null]);
        }
        
        return redirect()->back()->with('success', 'Hierarquia de unidades atualizada com sucesso.');
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
