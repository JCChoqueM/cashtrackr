<?php

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

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

it('prevent duplicate email address', function () {
    User::factory()->create([
        'email' => 'prueba@prueba',
    ]);

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Perez',
        'email' => 'prueba@prueba',
        'password' => '#123qweA',
        'password_confirmation' => '#123qweA',
    ]);
    $response->assertRedirect();

    $response->assertSessionHasErrors([
        'email' => 'El E-mail ya está registrado',
    ]);
});

it('sends the verification email notification after registration', function () {
    Notification::fake();


    $response = $this->post(route('register.store'), [
        'name' => 'Juan Perez',
        'email' => 'prueba@prueba.com',
        'password' => '#123qweA',
        'password_confirmation' => '#123qweA',
    ]);

    $user = User::where('email', 'prueba@prueba.com')->first();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('verifies the user email from a signed verification link', function () {

    $user = User::factory()->unverified()->create();


    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('dashboard'));
    // \dd($user);
    expect($user->hasVerifiedEmail())->toBeTrue();
});

it('does not allow an unverified user to access the dashboard', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

it('allows a verified user to access the dashboard', function () {

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});
