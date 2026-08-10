<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $search = Str::upper($request->input('search'));

        // Query base
        $query = Directory::query();

        // Aplicar filtros si existe una búsqueda
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('NOMBRE', 'LIKE', "%{$search}%")
                  ->orWhere('PUESTO', 'LIKE', "%{$search}%")
                  ->orWhere('COORDINACION', 'LIKE', "%{$search}%");
            });
        }

        // Paginación de 15 en 15 para evitar lentitud si el CSV es gigante
        $contactos = $query->orderBy('NOMBRE', 'ASC')->paginate(15)->withQueryString();

        return view('directory.index', compact('contactos', 'search'));
    }
}