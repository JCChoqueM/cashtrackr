<?php

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
