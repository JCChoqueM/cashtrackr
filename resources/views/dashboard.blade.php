@extends('layouts.auth')

@section('title')
    Administra tus presuestos
@endsection

@section('auth-contents')
@if (session('success'))
 <x-alert  :message="session('success')" />
    
@endif

@endsection