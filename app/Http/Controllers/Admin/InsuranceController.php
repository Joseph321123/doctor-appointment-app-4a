<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $insurances = Insurance::latest()->get();
        return view('admin.insurances.index', compact('insurances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.insurances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'notas_adicionales' => 'nullable|string',
        ]);

        Insurance::create($data);

        return redirect()
            ->route('admin.insurances.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Aseguradora registrada correctamente.',
            ]);
    }
}
