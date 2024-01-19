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
        'latitude'    => '01.23456789',
        'longitude'   => '123.45678910',
    ]);
    expect($response->status())->toBe(302);
    
    $unit = Unit::where('name', 'Unit Test')->first();
    expect($unit->name)->toBe('Unit Test');
    expect($unit->description)->toBe('Description of Unit Test');
    expect($unit->email)->toBe('unit@test.com');
    expect($unit->phone)->toBe('123456789');
    expect($unit->latitude)->toBe('1.23456789');
    expect($unit->longitude)->toBe('1234567891');

    $response->assertRedirect('/');
});
