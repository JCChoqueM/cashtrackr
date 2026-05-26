@extends('layouts.auth')

@section('title')
    Administra tus presuestos
@endsection

@section('auth-contents')
@if (session('success'))
<p class="my-10 text-center border border-green-400 bg-green-100 text-green-700 py-3 text-sm" >
    {{ session('success') }}
</p>
    
@endif

@endsection