<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(): User
    {
        return User::factory()->create(['role' => 'owner']);
    }

    private function makeTenant(): User
    {
        return User::factory()->create(['role' => 'tenant']);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeApprovedProperty(User $owner): Property
    {
        return Property::factory()->create([
            'owner_id' => $owner->id,
            'status'   => 'approved',
        ]);
    }

    private function propertyPayload(array $overrides = []): array
    {
        return array_merge([
            'title'            => 'Cozy 2-Bedroom Apartment in Bole',
            'description'      => 'A beautiful and fully furnished apartment located in the heart of Bole sub-city, Addis Ababa. Close to Edna Mall and restaurants.',
            'type'             => 'apartment',
            'price'            => 25000,
            'city'             => 'Addis Ababa',
            'sub_city'         => 'Bole',
            'address'          => 'Bole Atlas, Near Edna Mall',
            'bedrooms'         => 2,
            'bathrooms'        => 1,
            'kitchens'         => 1,
            'furnishing_status'=> 'furnished',
            'size_sqm'         => 85,
            'available_date'   => now()->addDays(5)->format('Y-m-d'),
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Public Listing
    |--------------------------------------------------------------------------
    */
    public function test_anyone_can_browse_approved_properties(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->count(3)->create(['owner_id' => $owner->id, 'status' => 'approved']);
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->getJson('/api/properties');

        $response->assertOk();
        // Only approved ones returned
        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_pending_properties_not_shown_in_public_listing(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->getJson('/api/properties');

        $response->assertOk();
        $this->assertEquals(0, $response->json('meta.total'));
    }

    public function test_can_search_properties_by_title(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'title' => 'Luxury Villa in Bole']);
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'title' => 'Studio Apartment']);

        $response = $this->getJson('/api/properties?search=Luxury');

        $response->assertOk();
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('Luxury Villa in Bole', $response->json('data.0.title'));
    }

    public function test_can_filter_properties_by_city(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'city' => 'Addis Ababa']);
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'city' => 'Bahir Dar']);

        $response = $this->getJson('/api/properties?city=Bahir+Dar');

        $response->assertOk();
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('Bahir Dar', $response->json('data.0.city'));
    }

    public function test_can_filter_by_price_range(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'price' => 10000]);
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'price' => 30000]);
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'price' => 60000]);

        $response = $this->getJson('/api/properties?min_price=15000&max_price=45000');

        $response->assertOk();
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals(30000, $response->json('data.0.price'));
    }

    public function test_can_sort_by_price_ascending(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'price' => 50000]);
        Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved', 'price' => 10000]);

        $response = $this->getJson('/api/properties?sort=price_asc');

        $response->assertOk();
        $this->assertEquals(10000, $response->json('data.0.price'));
    }

    public function test_property_listing_is_paginated(): void
    {
        $owner = $this->makeOwner();
        Property::factory()->count(20)->create(['owner_id' => $owner->id, 'status' => 'approved']);

        $response = $this->getJson('/api/properties?per_page=5');

        $response->assertOk();
        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(20, $response->json('meta.total'));
    }

    public function test_can_view_single_approved_property(): void
    {
        $owner = $this->makeOwner();
        $property = $this->makeApprovedProperty($owner);

        $response = $this->getJson("/api/properties/{$property->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $property->id);
    }

    public function test_cannot_view_pending_property_as_guest(): void
    {
        $owner = $this->makeOwner();
        $pending = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->getJson("/api/properties/{$pending->id}");

        $response->assertForbidden();
    }

    public function test_owner_can_view_their_own_pending_property(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($owner)->getJson("/api/properties/{$property->id}");

        $response->assertOk();
    }

    /*
    |--------------------------------------------------------------------------
    | Property Creation
    |--------------------------------------------------------------------------
    */
    public function test_owner_can_create_property(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner();
        $amenity = Amenity::factory()->create();

        $payload = $this->propertyPayload(['amenity_ids' => [$amenity->id]]);

        $response = $this->actingAs($owner)->postJson('/api/owner/properties', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.owner.id', $owner->id);

        $this->assertDatabaseHas('properties', ['title' => $payload['title'], 'owner_id' => $owner->id]);
    }

    public function test_new_property_starts_as_pending(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner();

        $response = $this->actingAs($owner)->postJson('/api/owner/properties', $this->propertyPayload());

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_tenant_cannot_create_property(): void
    {
        $tenant = $this->makeTenant();

        $response = $this->actingAs($tenant)->postJson('/api/owner/properties', $this->propertyPayload());

        $response->assertForbidden();
    }

    public function test_guest_cannot_create_property(): void
    {
        $response = $this->postJson('/api/owner/properties', $this->propertyPayload());

        $response->assertUnauthorized();
    }

    public function test_property_creation_validates_required_fields(): void
    {
        $owner = $this->makeOwner();

        $response = $this->actingAs($owner)->postJson('/api/owner/properties', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'type', 'price', 'city', 'bedrooms', 'bathrooms', 'kitchens', 'furnishing_status']);
    }

    public function test_owner_can_upload_images_with_property(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner();

        $payload = $this->propertyPayload();
        $payload['images'] = [
            UploadedFile::fake()->image('living-room.jpg', 800, 600),
            UploadedFile::fake()->image('bedroom.jpg', 800, 600),
        ];
        $payload['primary_image_index'] = 0;

        $response = $this->actingAs($owner)->postJson('/api/owner/properties', $payload);

        $response->assertStatus(201);
        $propertyId = $response->json('data.id');
        $this->assertDatabaseHas('property_images', ['property_id' => $propertyId, 'is_primary' => true]);
        $this->assertEquals(2, \App\Models\PropertyImage::where('property_id', $propertyId)->count());
    }

    public function test_rejects_invalid_image_type(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner();

        $payload = $this->propertyPayload();
        $payload['images'] = [UploadedFile::fake()->create('document.pdf', 100, 'application/pdf')];

        $response = $this->actingAs($owner)->postJson('/api/owner/properties', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);
    }

    /*
    |--------------------------------------------------------------------------
    | Property Editing
    |--------------------------------------------------------------------------
    */
    public function test_owner_can_update_their_property(): void
    {
        $owner = $this->makeOwner();
        $property = $this->makeApprovedProperty($owner);

        $response = $this->actingAs($owner)->putJson("/api/owner/properties/{$property->id}", [
            'title' => 'Updated Title For Testing',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated Title For Testing');

        $this->assertDatabaseHas('properties', ['id' => $property->id, 'title' => 'Updated Title For Testing']);
    }

    public function test_content_change_resets_approved_property_to_pending(): void
    {
        $owner = $this->makeOwner();
        $property = $this->makeApprovedProperty($owner);

        $response = $this->actingAs($owner)->putJson("/api/owner/properties/{$property->id}", [
            'title' => 'Completely New Title That Changed',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_owner_cannot_update_another_owners_property(): void
    {
        $owner1 = $this->makeOwner();
        $owner2 = $this->makeOwner();
        $property = $this->makeApprovedProperty($owner1);

        $response = $this->actingAs($owner2)->putJson("/api/owner/properties/{$property->id}", [
            'title' => 'Hijacked Title',
        ]);

        $response->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Property Deletion
    |--------------------------------------------------------------------------
    */
    public function test_owner_can_delete_their_property(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->deleteJson("/api/owner/properties/{$property->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Property deleted.');

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_owner_cannot_delete_another_owners_property(): void
    {
        $owner1 = $this->makeOwner();
        $owner2 = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner1->id]);

        $response = $this->actingAs($owner2)->deleteJson("/api/owner/properties/{$property->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Approval
    |--------------------------------------------------------------------------
    */
    public function test_admin_can_approve_a_pending_property(): void
    {
        $admin = $this->makeAdmin();
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($admin)
            ->patchJson("/api/admin/properties/{$property->id}/status", ['status' => 'approved']);

        $response->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $this->assertDatabaseHas('properties', ['id' => $property->id, 'status' => 'approved']);
    }

    public function test_admin_can_reject_a_property(): void
    {
        $admin = $this->makeAdmin();
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($admin)
            ->patchJson("/api/admin/properties/{$property->id}/status", ['status' => 'rejected']);

        $response->assertOk()
            ->assertJsonPath('data.status', 'rejected');
    }

    public function test_owner_cannot_approve_their_own_property(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($owner)
            ->patchJson("/api/admin/properties/{$property->id}/status", ['status' => 'approved']);

        // Owner is blocked by role:admin middleware
        $response->assertForbidden();
    }

    public function test_tenant_cannot_approve_property(): void
    {
        $tenant = $this->makeTenant();
        $owner  = $this->makeOwner();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($tenant)
            ->patchJson("/api/admin/properties/{$property->id}/status", ['status' => 'approved']);

        $response->assertForbidden();
    }
}
