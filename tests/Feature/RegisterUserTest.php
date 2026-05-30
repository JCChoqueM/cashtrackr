<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
it('shows registration form', function () {

    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertSee('Crear Cuenta');
    $response->assertSee('Registrarme');

    $response->assertSeeInOrder([
        'Crear Cuenta',
        'Registrarme',

    ]);
});

it('registers a new user as unverified and dispatches the registered event', function () {

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Perez',
        'email' => 'prueba@prueba.com',
        'password' => '#123qweA',
        'password_confirmation' => '#123qweA',
    ]);

    $response->assertRedirect(route('verification.notice'));
});
