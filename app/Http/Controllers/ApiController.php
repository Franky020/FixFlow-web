<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    // Obtener lista de users
    public function getUsers()
    {
        $response = Http::get(env('API_URL') . 'users/');

        if ($response->successful()) {
            $data = $response->json();
        } else {
            $data = [];
        }

        return view('user.index', compact('data'));
    }

    // Crear un user
    public function createUser(Request $request)
    {
        // Validar datos
        $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'phone'      => 'required|string',
            'address'    => 'required|string',
            'user_type'  => 'required|string',
            'age'        => 'required|integer',
            'rfc'        => 'required|string',
            'password'   => 'required|string',
            'company'    => 'required|integer',
        ]);

        $response = Http::post(env('API_URL') . 'users/', [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'user_type'  => $request->user_type,
            'age'        => $request->age,
            'rfc'        => $request->rfc,
            'password'   => $request->password,
            'company'    => $request->company,
        ]);

        return redirect()->route('user.list');
    }
}
