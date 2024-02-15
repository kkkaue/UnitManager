<?php

use App\Models\Unit;
use App\Models\User;

it('should be able to update a unit', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $updatedData = [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'location'    => [
            'latitude'  => -23.5505199,
            'longitude' => -46.6333094,
        ],
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
        'parent_id'   => 1,
    ];

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), $updatedData);

    $response
        ->assertRedirect()
        ->assertSessionHas('success', 'Unidade atualizada com sucesso.');

    $unit->refresh();
    expect($unit->name)->toBe($updatedData['name']);
});

it('should redirect unauthenticated user to login page', function () {
    $unit = Unit::factory()->create();

    $response = $this->putJson(route('units.update', $unit->id), [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
    ]);

    expect($response)->assertUnauthorized();
});

it('should return error when unit name is not provided', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'description' => 'Description of Unit Test Updated',
    ]);

    $response->assertJsonValidationErrors(['name' => 'O campo nome é obrigatório.']);
});

it('should return error when unit description is not provided', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'name' => 'Unit Test Updated',
    ]);

    $response->assertJsonValidationErrors(['description' => 'O campo descrição é obrigatório.']);
});

it('should return error when unit email is not provided', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
    ]);

    $response->assertJsonValidationErrors(['email' => 'O campo e-mail é obrigatório.']);
});

it('should return error when unit email is not valid', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
        'email'       => 'invalid-email',
    ]);

    $response->assertJsonValidationErrors(['email' => 'O e-mail informado é inválido.']);
});

it('should return error when unit phone is not provided', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
        'email'       => 'unit@test.com',
    ]);

    $response->assertJsonValidationErrors(['phone' => 'O campo telefone é obrigatório.']);
});

it('should return error when unit latitude is not provided', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
        'email'       => 'unit@test.com'
    ]);

    $response->assertJsonValidationErrors(['latitude' => 'Você deve informar a localização da unidade.']);
});

it('should return error when unit latitude is not valid', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->putJson(route('units.update', $unit->id), [
        'name'        => 'Unit Test Updated',
        'description' => 'Description of Unit Test Updated',
        'email'       => 'unit@test.com',
        'latitude'    => 'invalid-latitude',
    ]);

    $response->assertJsonValidationErrors(['latitude' => 'A localização informada é inválida.']);
});

it('should return error when unit longitude is not provided', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('units.update', $unit->id), [
            'name'        => 'Unit Test Updated',
            'description' => 'Description of Unit Test Updated',
            'email'       => 'unit@test.com'
        ]);

    $response->assertJsonValidationErrors(['longitude' => 'Você deve informar a localização da unidade.']);

});

it('should return error when unit longitude is not valid', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('units.update', $unit->id), [
            'name'        => 'Unit Test Updated',
            'description' => 'Description of Unit Test Updated',
            'email'       => 'unit@test.com',
            'longitude'   => 'invalid-longitude',
        ]);

    $response->assertJsonValidationErrors(['longitude' => 'A localização informada é inválida.']);
});

it('should return error when unit does not exist', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('units.update', 'invalid-id'), [
            'name'        => 'Unit Test Updated',
            'description' => 'Description of Unit Test Updated',
            'email'       => 'unit@test.com',
            'phone'       => '123456789',
            'latitude'    => -23.5505199,
            'longitude'   => -46.6333094,
            'location'    => [
                'latitude'  => -23.5505199,
                'longitude' => -46.6333094,
            ],
            'parent_id'   => $unit->id,
        ]);

    $response->assertNotFound();
});
