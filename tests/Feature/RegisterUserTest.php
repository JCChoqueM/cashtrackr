<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

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
    Event::fake();


    $response = $this->post(route('register.store'), [
        'name' => 'Juan Perez',
        'email' => 'prueba@prueba.com',
        'password' => '#123qweA',
        'password_confirmation' => '#123qweA',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $user = User::where('email', 'prueba@prueba.com')->first();

    expect($user)->not()->toBeNull();
    expect($user->name)->toBe('Juan Perez');
    expect($user->email)->toBe('prueba@prueba.com');
    expect($user->hasVerifiedEmail())->toBeFalse();

    Event::assertDispatched(Registered::class);
});

it('should validate required fields when the request body is empty', function () {
    $response = $this->post(route('register.store'), []);

    $response->assertSessionHasErrors([
        'name',
        'email',
        'password',
    ]);
    $response->assertSessionHasErrors([
        'name' => 'El Nombre es obligatorio',
        'email' => 'El E-mail es obligatorio',
        'password' => 'La Contraseña es obligatorio',
    ]);
});
