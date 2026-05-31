<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the login screen', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});