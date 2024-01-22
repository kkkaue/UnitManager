<?php

use App\Models\Unit;
use App\Models\User;

it('should be able to create a unit', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);
    expect($response)
        ->assertRedirect(route('home'))
        ->assertSessionHas('success', 'Unidade criada com sucesso.');

    $unit = Unit::where('name', 'Unit Test')->first();
    expect($unit->name)->toBe('Unit Test');
    expect($unit->description)->toBe('Description of Unit Test');
    expect($unit->email)->toBe('unit@test.com');
    expect($unit->phone)->toBe('123456789');
    expect((float)$unit->latitude)->toBe(-23.5505199);
    expect((float)$unit->longitude)->toBe(-46.6333094);
});

it('should redirect unauthenticated user to login page', function () {
    $response = $this->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertRedirect(route('login'));
});

it('should return error when unit name is not provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['name' => 'The name field is required.']);
});

it('should return error when unit description is not provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'      => 'Unit Test',
        'email'     => 'unit@test.com',
        'phone'     => '123456789',
        'latitude'  => -23.5505199,
        'longitude' => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['description' => 'The description field is required.']);
});

it('should return error when unit email is not provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['email' => 'The email field is required.']);
});

it('should return error when unit phone is not provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['phone' => 'The phone field is required.']);
});

it('should return error when unit latitude is not provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['latitude' => 'The latitude field is required.']);
});

it('should return error when unit longitude is not provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
    ]);

    expect($response)->assertSessionHasErrors(['longitude' => 'The longitude field is required.']);
});

it('should return error when email format is invalid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'invalid-email-format',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['email' => 'The email field must be a valid email address.']);
});
