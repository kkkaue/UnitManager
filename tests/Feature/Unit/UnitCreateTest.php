<?php

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('should be able to create a unit', function () {
    $user = User::factory()->create();
    
    expect(Auth::check())->toBeFalse();

    $this->actingAs($user);

    expect(Auth::check())->toBeTrue();
    
    $response = $this->post(route('units.store'), [
        'name'        => 'Unit Test',
        'description' => 'Description of Unit Test',
        'email'       => 'unit@test.com',
        'phone'       => '123456789',
        'latitude'    => -23.5505199,
        'longitude'   => -46.6333094,
    ]);
    expect($response->status())->toBe(302);
    
    $unit = Unit::where('name', 'Unit Test')->first();
    expect($unit->name)->toBe('Unit Test');
    expect($unit->description)->toBe('Description of Unit Test');
    expect($unit->email)->toBe('unit@test.com');
    expect($unit->phone)->toBe('123456789');
    expect((float)$unit->latitude)->toBe(-23.5505199);
    expect((float)$unit->longitude)->toBe(-46.6333094);
    $response->assertRedirect('/');
});
