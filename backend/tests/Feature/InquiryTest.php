<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwnerWithProperty(): array
    {
        $owner    = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved']);
        return [$owner, $property];
    }

    private function inquiryPayload(array $overrides = []): array
    {
        return array_merge([
            'message'        => 'I am interested in viewing this property at your earliest convenience. Please let me know when it is available.',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Sending Inquiries
    |--------------------------------------------------------------------------
    */
    public function test_tenant_can_send_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)
            ->postJson("/api/properties/{$property->id}/inquiries", $this->inquiryPayload());

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('inquiries', [
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'status'      => 'pending',
        ]);
    }

    public function test_owner_cannot_send_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();

        $response = $this->actingAs($owner)
            ->postJson("/api/properties/{$property->id}/inquiries", $this->inquiryPayload());

        // Owner is blocked by role:tenant middleware
        $response->assertForbidden();
    }

    public function test_guest_cannot_send_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();

        $response = $this->postJson("/api/properties/{$property->id}/inquiries", $this->inquiryPayload());

        $response->assertUnauthorized();
    }

    public function test_cannot_send_duplicate_active_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        // First inquiry
        $this->actingAs($tenant)->postJson("/api/properties/{$property->id}/inquiries", $this->inquiryPayload());

        // Second inquiry (duplicate)
        $response = $this->actingAs($tenant)
            ->postJson("/api/properties/{$property->id}/inquiries", $this->inquiryPayload());

        $response->assertStatus(422);
        $this->assertEquals(1, Inquiry::where('tenant_id', $tenant->id)->where('property_id', $property->id)->count());
    }

    public function test_cannot_inquire_on_pending_property(): void
    {
        $owner    = User::factory()->owner()->create();
        $pending  = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);
        $tenant   = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)
            ->postJson("/api/properties/{$pending->id}/inquiries", $this->inquiryPayload());

        $response->assertStatus(422);
    }

    public function test_inquiry_message_is_required(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)
            ->postJson("/api/properties/{$property->id}/inquiries", ['message' => '']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant View
    |--------------------------------------------------------------------------
    */
    public function test_tenant_can_view_their_own_inquiries(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        Inquiry::create([
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'message'     => 'Test inquiry message here.',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($tenant)->getJson('/api/tenant/inquiries');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_tenant_cannot_see_another_tenants_inquiries(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant1 = User::factory()->create(['role' => 'tenant']);
        $tenant2 = User::factory()->create(['role' => 'tenant']);

        Inquiry::create([
            'property_id' => $property->id,
            'tenant_id'   => $tenant1->id,
            'message'     => 'tenant1 inquiry here.',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($tenant2)->getJson('/api/tenant/inquiries');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    /*
    |--------------------------------------------------------------------------
    | Owner Management
    |--------------------------------------------------------------------------
    */
    public function test_owner_can_view_inquiries_on_their_properties(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        Inquiry::create([
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'message'     => 'I would like to visit the apartment.',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($owner)->getJson('/api/owner/inquiries');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_owner_can_accept_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        $inquiry = Inquiry::create([
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'message'     => 'Looking to rent the apartment.',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($owner)
            ->patchJson("/api/owner/inquiries/{$inquiry->id}/status", ['status' => 'accepted']);

        $response->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->assertDatabaseHas('inquiries', ['id' => $inquiry->id, 'status' => 'accepted']);
    }

    public function test_owner_can_reject_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        $inquiry = Inquiry::create([
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'message'     => 'I am interested in this rental.',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($owner)
            ->patchJson("/api/owner/inquiries/{$inquiry->id}/status", ['status' => 'rejected']);

        $response->assertOk()
            ->assertJsonPath('data.status', 'rejected');
    }

    public function test_owner_cannot_manage_another_owners_inquiry(): void
    {
        [$owner1, $property1] = $this->makeOwnerWithProperty();
        [$owner2, $property2] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        // Inquiry on owner1's property
        $inquiry = Inquiry::create([
            'property_id' => $property1->id,
            'tenant_id'   => $tenant->id,
            'message'     => 'Some inquiry message here.',
            'status'      => 'pending',
        ]);

        // owner2 tries to update it
        $response = $this->actingAs($owner2)
            ->patchJson("/api/owner/inquiries/{$inquiry->id}/status", ['status' => 'accepted']);

        $response->assertForbidden();
    }

    public function test_after_rejection_tenant_can_send_new_inquiry(): void
    {
        [$owner, $property] = $this->makeOwnerWithProperty();
        $tenant = User::factory()->create(['role' => 'tenant']);

        // First inquiry — gets rejected
        $inquiry = Inquiry::create([
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'message'     => 'First inquiry message here.',
            'status'      => 'rejected',
        ]);

        // Tenant can send another since the previous was rejected
        $response = $this->actingAs($tenant)
            ->postJson("/api/properties/{$property->id}/inquiries", $this->inquiryPayload());

        $response->assertStatus(201);
    }
}
