<?php

use App\Models\Unit;

it('tests the unit hierarchy', function () {
    $unit1 = Unit::factory()->create(['name' => 'Unit 1']);
    $unit2 = Unit::factory()->create(['name' => 'Unit 2', 'parent_id' => $unit1->id]);
    $unit3 = Unit::factory()->create(['name' => 'Unit 3', 'parent_id' => $unit2->id]);

    expect($unit1->children->count())->toBe(1);
    expect($unit1->children->first()->name)->toBe('Unit 2');
    expect($unit1->children->first()->children->count())->toBe(1);
    expect($unit1->children->first()->children->first()->name)->toBe('Unit 3');
    expect($unit1->children->first()->children->first()->children->count())->toBe(0);

    expect($unit2->children->count())->toBe(1);
    expect($unit2->children->first()->name)->toBe('Unit 3');
    expect($unit2->children->first()->children->count())->toBe(0);

    expect($unit3->children->count())->toBe(0);

    expect($unit1->parent)->toBeNull();

    expect($unit2->parent->name)->toBe('Unit 1');

    expect($unit3->parent->name)->toBe('Unit 2');
});

it('tests removing a parent unit', function () {
    $unit1 = Unit::factory()->create(['name' => 'Unit 1']);
    $unit2 = Unit::factory()->create(['name' => 'Unit 2', 'parent_id' => $unit1->id]);
    $unit3 = Unit::factory()->create(['name' => 'Unit 3']);

    $unit2->parent_id = $unit3->id;
    $unit2->save();

    $unit1->delete();

    expect($unit2->fresh()->parent->name)->toBe('Unit 3');
    expect($unit3->fresh()->children->count())->toBe(1);
});

it('tests moving a unit to a new parent', function () {
    $unit1 = Unit::factory()->create(['name' => 'Unit 1']);
    $unit2 = Unit::factory()->create(['name' => 'Unit 2', 'parent_id' => $unit1->id]);
    $unit3 = Unit::factory()->create(['name' => 'Unit 3']);

    $unit2->parent_id = $unit3->id;
    $unit2->save();

    expect($unit2->fresh()->parent->name)->toBe('Unit 3');

    expect($unit1->fresh()->children->count())->toBe(0);
});