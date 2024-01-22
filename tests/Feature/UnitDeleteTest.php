<?php

use App\Models\Unit;
use App\Models\User;

it('should be able to delete a unit', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $response = $this->actingAs($user)->delete(route('units.destroy', $unit->id));

    $response
        ->assertRedirect(route('home'))
        ->assertSessionHas('success', 'Unidade excluída com sucesso.');

    expect(Unit::find($unit->id))->toBeNull();
});

it('should redirect unauthenticated user to login page', function () {
    $unit = Unit::factory()->create();

    $response = $this->delete(route('units.destroy', $unit->id));

    expect($response)->assertRedirect(route('login'));
});

it('should return error when unit does not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('units.destroy', 'invalid-id'));

    expect($response)->assertNotFound();
});