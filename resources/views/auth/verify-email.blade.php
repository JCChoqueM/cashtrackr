@extends('layouts.auth')

@section('title')
    Confirma tu cuenta
@endsection

@section('auth-contents')
    <p class="mt-5 text-lg">tu cuenta fue creada con exito, revisa tu correo para confirmarla</p>

 
    <form
        method="POST"
        action="{{ route('verification.send') }}"
    >

        <input
            type="submit"
            class="bg-amber-500 w-full text-center mt-5 px-5 py-2 uppercase font-bold cursor-pointer"
            value="Renviar correo de verificacion"
        >

    </form>
@endsection
