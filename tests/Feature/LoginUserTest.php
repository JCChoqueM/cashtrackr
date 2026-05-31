<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the login screen', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

it('logs in a verified user successfully', function () {
    User::factory()->create([
        'email' => 'prueba@prueba.com',
        'password' => bcrypt('#123qweA'),
        'email_verified_at' => now(),
    ]);
    //  \dd($user);

    $response = $this->post(route('login.store'), [
        'email' => 'prueba@prueba.com',
        'password' => '#123qweA',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});

it('does not long in with invalid credentials', function () {
    User::factory()->create([
        'email' => 'prueba@prueba.com',
        'password' => bcrypt('#123qweA'),

    ]);
    //  \dd($user);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'prueba@prueba.com',
        'password' => 'incorrect-#123qweA',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas(
        'error',
        'Las credenciales son Incorrectas'
    );
    $this->assertGuest();
});

it('prevents unverified user from accessing dashboard', function () {
    User::factory()->unverified()->create([
        'email' => 'prueba@prueba.com',
        'password' => bcrypt('#123qweA'),

    ]);
    //  \dd($user);

    $response = $this->post(route('login.store'), [
        'email' => 'prueba@prueba.com',
        'password' => '#123qweA',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertRedirect(route('verification.notice'));
});

it('does not allow access to dashboard if email is not verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => null
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

it('allow access to dashboard if email is verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

it('fails login if user does not exist', function () {
    $response = $this
        ->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'noextists@noexists.com',
            'password' => '#123qweA',
        ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(
        [

            'email' => 'No encontramos un usuario con ese E-mail'
        ]
    );
    $this->assertGuest();
});
