<?php

use App\Models\Unit;
use App\Models\User;

it('should be able to update multiple unit parents in a single request', function () {
    $user = User::factory()->create();
    $parent = Unit::factory()->create(['id' => 1]);
    $otherParent = Unit::factory()->create(['id' => 2]);
    $anotherParent = Unit::factory()->create(['id' => 3]);

    // Criar várias unidades
    $units = Unit::factory()->count(3)->create(['parent_id' => $parent->id]);

    // Mapear as unidades para seus novos parent_ids
    $updates = [

        ['unit_id' => $units[0]->id, 'parent_id' => $otherParent->id],
        ['unit_id' => $units[1]->id, 'parent_id' => $anotherParent->id],
        ['unit_id' => $units[2]->id, 'parent_id' => $otherParent->id],
    ];

    $response = $this->actingAs($user)->postJson(route('units.update-hierarchy'), ['units' => $updates]);

    $response->assertStatus(302);

    foreach ($updates as $update) {
        expect(Unit::find($update['unit_id'])->parent_id)->toBe($update['parent_id']);
    }
});