<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MiQuinielaController extends Controller
{
    public function index()
    {
        return view('quiniela.mi-quiniela', [
            'user' => Auth::user(),
        ]);
    }
}