<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
     * Exibe o mapa com as unidades.
     */
    public function map()
    {
        $units = Unit::all();

        return Inertia::render('Units/Map', [
            'units' => $units,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $phone = $request->input('phone');
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $request->merge(['phone' => $phone]);

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

        $messages = [
            'name.required'        => 'O campo nome é obrigatório.',
            'name.max'             => 'O campo nome deve ter no máximo 255 caracteres.',
            'description.required' => 'O campo descrição é obrigatório.',
            'email.required'       => 'O campo e-mail é obrigatório.',
            'email.email'          => 'O e-mail informado é inválido.',
            'email.max'            => 'O campo e-mail deve ter no máximo 255 caracteres.',
            'email.unique'         => 'Este e-mail já está em uso.',
            'phone.required'       => 'O campo telefone é obrigatório.',
            'phone.max'            => 'O campo telefone deve ter no máximo 255 caracteres.',
            'latitude.required'    => 'Você deve informar a localização da unidade.',
            'latitude.numeric'     => 'A localização informada é inválida.',
            'longitude.required'   => 'Você deve informar a localização da unidade.',
            'longitude.numeric'    => 'A localização informada é inválida.',
            'parent_id.required'   => 'Você deve informar a unidade pai.',
            'parent_id.exists'     => 'A unidade pai informada não existe.',
        ];

        $request->validate($rules, $messages);

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

        return redirect()->back()->with('success', 'Unidade criada com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $phone = $request->input('phone');
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $request->merge(['phone' => $phone]);
        
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('units')->ignore($id)],
            'phone'       => ['required', 'string', 'max:255'],
            'location'    => ['required', 'array'], // 'latitude' e 'longitude
            'latitude'    => ['required', 'numeric'],
            'longitude'   => ['required', 'numeric'],
            'parent_id'   => 'nullable|exists:units,id',
        ];
        if ($id !== '1') {
            $rules['parent_id'] = 'required|exists:units,id';
        }

        $messages = [
            'name.required'        => 'O campo nome é obrigatório.',
            'name.max'             => 'O campo nome deve ter no máximo 255 caracteres.',
            'description.required' => 'O campo descrição é obrigatório.',
            'email.required'       => 'O campo e-mail é obrigatório.',
            'email.email'          => 'O e-mail informado é inválido.',
            'email.max'            => 'O campo e-mail deve ter no máximo 255 caracteres.',
            'email.unique'         => 'Este e-mail já está em uso.',
            'phone.required'       => 'O campo telefone é obrigatório.',
            'phone.max'            => 'O campo telefone deve ter no máximo 255 caracteres.',
            'latitude.required'    => 'Você deve informar a localização da unidade.',
            'latitude.numeric'     => 'A localização informada é inválida.',
            'longitude.required'   => 'Você deve informar a localização da unidade.',
            'longitude.numeric'    => 'A localização informada é inválida.',
            'parent_id.required'   => 'Você deve informar a unidade pai.',
            'parent_id.exists'     => 'A unidade pai informada não existe.',
            'parent_id.child'      => 'A unidade não pode ser atualizada para ser filha de uma de suas unidades filhas.',
        ];

        $request->validate($rules, $messages);

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

        $unit = Unit::findOrFail($id);

        // Verifica se a unidade não está sendo atualizada para ser filha de uma de suas unidades filhas
        $hasChildWithSameId = $unit->children()->where('id', $data['parent_id'])->exists();
        if ($hasChildWithSameId) {
            return redirect()->back()->withErrors(['parent_id' => $messages['parent_id.child']]);
        }
        
        $unit->update($data);

        return redirect()->back()->with('success', 'Unidade atualizada com sucesso.');
    }

    /**
     * Atualiza a hierarquia das unidades.
     */
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

        // Verifica se a unidade é uma unidade raiz
        if ($unit->parent_id === null) {
            return redirect()->back()->with('error', 'A unidade raiz não pode ser removida.');
        }

        // Verifica se a unidade possui filhos
        $childUnits = $unit->children;
        if ($childUnits->isNotEmpty()) {
            // Se tiver, altera o parent_id delas para um novo valor
            $unit->children()->update(['parent_id' => $unit->parent_id]);
        }
        $unit->delete();

        return redirect()->back()->with('success', 'Unidade removida com sucesso.');
    }
}
