<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PerfilController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('perfil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

    $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'clues'      => ['required', 'string', 'exists:catalogo_clues,clues'], // Valida que exista en tu catálogo
            'department' => ['required', 'string'],
        ], [
            'clues.exists' => 'LA CLUES SELECCIONADA NO ES VÁLIDA O NO EXISTE.',
        ]);

        $user->update([
            'name'       => $request->input('name'),
            'email'      => $request->input('email'),
            'clues'      => $request->input('clues'),
            'department' => $request->input('department'),
        ]);

        return redirect()->route('dashboard')->with('success', 'PERFIL ACTUALIZADO CORRECTAMENTE.');
    }

    public function buscarClues(Request $request)
    {
        $search = Str::upper($request->input('query'));
        if(empty($search)) {
            return response()->json([]);
        }

        $resultados= DB::table('catalogo_clues')
            ->select('clues', 'nombre_unidad')
            ->where ('nombre_unidad', 'LIKE', "%{$search}%")
            ->orWhere('clues', 'LIKE', "%{$search}%")
            ->orderBy('nombre_unidad', 'ASC')
            ->limit(10) 
            ->get();
        return response()->json($resultados);
    }
}