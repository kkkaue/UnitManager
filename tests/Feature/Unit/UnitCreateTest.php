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
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Unidade criada com sucesso.');

    $unit = Unit::where('name', 'Unit Test')->first();
    expect($unit->name)->toBe('Unit Test');
    expect($unit->description)->toBe('Description of Unit Test');
    expect($unit->email)->toBe('unit@test.com');
    expect($unit->phone)->toBe('123456789');
    expect((float)$unit->latitude)->toBe(-23.5505199);
    expect((float)$unit->longitude)->toBe(-46.6333094);
});

it('should be able to create a unit even after there are already created units', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
        'parent_id'   => $unit->id,
    ]);
    expect($response)
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Unidade criada com sucesso.');
});

it('should be mandatory to pass the ID of the parent unit if there are already units', function () {
    $user = User::factory()->create();
    Unit::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
        // 'parent_id' não é fornecido
    ]);

    expect($response)->assertSessionHasErrors('parent_id');

    $unit = Unit::where('name', 'Unit Test')->first();
    expect($unit)->toBeNull();
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

    expect($response)->assertSessionHasErrors(['name' => 'O campo nome é obrigatório.']);
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

    expect($response)->assertSessionHasErrors(['description' => 'O campo descrição é obrigatório.']);
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

    expect($response)->assertSessionHasErrors(['email' => 'O campo e-mail é obrigatório.']);
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

    expect($response)->assertSessionHasErrors(['phone' => 'O campo telefone é obrigatório.']);
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

    expect($response)->assertSessionHasErrors(['latitude' => 'Você deve informar a localização da unidade.']);
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

    expect($response)->assertSessionHasErrors(['longitude' => 'Você deve informar a localização da unidade.']);
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

    expect($response)->assertSessionHasErrors(['email' => 'O e-mail informado é inválido.']);
});

it('should return error when latitude format is invalid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => 'invalid-latitude-format',
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['latitude' => 'A localização informada é inválida.']);
});

it('should return error when longitude format is invalid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => 'invalid-longitude-format',
    ]);

    expect($response)->assertSessionHasErrors(['longitude' => 'A localização informada é inválida.']);
});

it('should return error when unit name is longer than 255 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => str_repeat('a', 256),
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['name' => 'O campo nome deve ter no máximo 255 caracteres.']);

    $unit = Unit::where('name', str_repeat('a', 256))->first();
    expect($unit)->toBeNull();
});

it('should return error when unit email is longer than 255 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => str_repeat('a', 256) . '@teste.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['email' => 'O campo e-mail deve ter no máximo 255 caracteres.']);

    $unit = Unit::where('email', str_repeat('a', 256))->first();
    expect($unit)->toBeNull();
});

it('should return error when unit phone is longer than 255 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => str_repeat('a', 256),
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['phone' => 'O campo telefone deve ter no máximo 255 caracteres.']);

    $unit = Unit::where('phone', str_repeat('a', 256))->first();
    expect($unit)->toBeNull();
});

it('should return error when unit email is not unique', function () {
    $user = User::factory()->create();
    Unit::factory()->create(['email' => 'unit@test.com']);

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);

    expect($response)->assertSessionHasErrors(['email' => 'Este e-mail já está em uso.']);
});
