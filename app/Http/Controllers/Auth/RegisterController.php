<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SingupRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }


    public function store(SingupRequest $request)
    {
        $data =  $request->validated();

        // almacena la base de datos
       $user= User::create($data);

    event(new Registered($user));




    }
}
