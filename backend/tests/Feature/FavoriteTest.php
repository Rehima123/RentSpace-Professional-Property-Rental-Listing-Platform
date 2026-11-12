<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private function makeApprovedProperty(): Property
    {
        $owner = User::factory()->owner()->create();
        return Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved']);
    }

    public function test_authenticated_user_can_add_favorite(): void
    {
        $tenant   = User::factory()->create(['role' => 'tenant']);
        $property = $this->makeApprovedProperty();

        $response = $this->actingAs($tenant)
            ->postJson("/api/properties/{$property->id}/favorite");

        $response->assertStatus(201)
            ->assertJsonPath('favorited', true);

        $this->assertDatabaseHas('favorites', [
            'user_id'     => $tenant->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_toggling_favorite_twice_removes_it(): void
    {
        $tenant   = User::factory()->create(['role' => 'tenant']);
        $property = $this->makeApprovedProperty();

        // Add
        $this->actingAs($tenant)->postJson("/api/properties/{$property->id}/favorite");
        // Remove
        $response = $this->actingAs($tenant)->postJson("/api/properties/{$property->id}/favorite");

        $response->assertOk()
            ->assertJsonPath('favorited', false);

        $this->assertDatabaseMissing('favorites', [
            'user_id'     => $tenant->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_cannot_duplicate_favorites(): void
    {
        $tenant   = User::factory()->create(['role' => 'tenant']);
        $property = $this->makeApprovedProperty();

        // Add once
        $this->actingAs($tenant)->postJson("/api/properties/{$property->id}/favorite");
        // Add again (should toggle OFF, not create duplicate)
        $this->actingAs($tenant)->postJson("/api/properties/{$property->id}/favorite");

        $this->assertEquals(0, Favorite::where('user_id', $tenant->id)->where('property_id', $property->id)->count());
    }

    public function test_guest_cannot_favorite(): void
    {
        $property = $this->makeApprovedProperty();

        $response = $this->postJson("/api/properties/{$property->id}/favorite");

        $response->assertUnauthorized();
    }

    public function test_user_can_list_their_favorites(): void
    {
        $tenant   = User::factory()->create(['role' => 'tenant']);
        $property = $this->makeApprovedProperty();

        Favorite::create(['user_id' => $tenant->id, 'property_id' => $property->id]);

        $response = $this->actingAs($tenant)->getJson('/api/favorites');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_favorites_list_only_shows_own_favorites(): void
    {
        $tenant1 = User::factory()->create(['role' => 'tenant']);
        $tenant2 = User::factory()->create(['role' => 'tenant']);
        $property = $this->makeApprovedProperty();

        Favorite::create(['user_id' => $tenant1->id, 'property_id' => $property->id]);

        $response = $this->actingAs($tenant2)->getJson('/api/favorites');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }
}
