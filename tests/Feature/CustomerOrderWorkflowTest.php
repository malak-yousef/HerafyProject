<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Profession;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class CustomerOrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_craftman_cannot_create_customer_order(): void
    {
        $payload = $this->validPayload();

        $this->post(route('herafy.customer.orders.store'), $payload)->assertRedirect(route('login'));

        $craftman = Craftman::factory()->create();
        $this->actingAs($craftman->user, 'craftman')
            ->post(route('herafy.customer.orders.store'), $payload)
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_validation_errors_are_summarised_once_at_the_top_of_the_herafy_page(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->from(route('request'))
            ->post(route('herafy.customer.orders.store'), ['title' => ''])
            ->assertRedirect(route('request'))
            ->assertSessionHasErrors();

        $page = $this->actingAs($customer->user, 'customer')
            ->get(route('request'))
            ->assertOk();

        $page->assertSee('يرجى تصحيح الأخطاء التالية:')
            ->assertSee('validation-summary', false);

        $this->assertSame(
            1,
            substr_count($page->getContent(), 'validation-summary'),
            'The validation summary must be rendered exactly once per page.'
        );
    }

    public function test_request_form_renders_field_level_validation_feedback(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->from(route('request'))
            ->post(route('herafy.customer.orders.store'), ['title' => ''])
            ->assertRedirect(route('request'))
            ->assertSessionHasErrors(['profession_id', 'city_id', 'area_id', 'title', 'description']);

        $content = $this->actingAs($customer->user, 'customer')
            ->get(route('request'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            'is-invalid',
            $content,
            'The request form must mark invalid controls instead of relying only on the global summary.'
        );

        foreach (['profession_id', 'city_id', 'area_id', 'title', 'description'] as $field) {
            $this->assertStringContainsString('id="'.$field.'-error"', $content, $field.' must render its own error message.');
            $this->assertStringContainsString('aria-describedby="'.$field.'-error"', $content, $field.' must reference its error message for screen readers.');
        }
    }

    public function test_customer_creates_pending_order_with_server_side_ownership(): void
    {
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();
        $payload = $this->validPayload([
            'customer_id' => $otherCustomer->id,
            'status' => 'completed',
        ]);

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.orders.store'), $payload)
            ->assertRedirect(route('request'));

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'profession_id' => $payload['profession_id'],
            'status' => 'pending',
            'title' => $payload['title'],
        ]);
    }

    public function test_customer_order_validation_rejects_mismatch_negative_budget_and_past_date(): void
    {
        $customer = Customer::factory()->create();
        $otherArea = Area::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->from(route('request'))
            ->post(route('herafy.customer.orders.store'), $this->validPayload([
                'area_id' => $otherArea->id,
                'budget' => -1,
                'service_date' => now()->subDay()->toDateString(),
            ]))
            ->assertRedirect(route('request'))
            ->assertSessionHasErrors(['area_id', 'budget', 'service_date']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_soft_deleted_or_inactive_references_are_rejected(): void
    {
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'in_active']);
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);
        $area->delete();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.orders.store'), $this->validPayload([
                'profession_id' => $profession->id,
                'city_id' => $city->id,
                'area_id' => $area->id,
            ]))
            ->assertSessionHasErrors(['profession_id', 'area_id']);
    }

    public function test_valid_images_are_linked_to_new_order(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.orders.store'), $this->validPayload([
                'images' => [
                    $this->fakeImage('one.png'),
                    $this->fakeImage('two.png'),
                ],
            ]))
            ->assertRedirect(route('request'));

        $order = Order::sole();
        $this->assertCount(2, $order->images);
        $order->images->each(fn ($image) => Storage::disk('public')->assertExists($image->image_path));
    }

    public function test_invalid_image_is_rejected_without_creating_order(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.orders.store'), $this->validPayload([
                'images' => [UploadedFile::fake()->create('payload.txt', 10, 'text/plain')],
            ]))
            ->assertSessionHasErrors('images.0');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_image_persistence_failure_rolls_back_database_and_stored_file(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();

        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'insert into "order_images"')) {
                throw new RuntimeException('Simulated image persistence failure.');
            }
        });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($customer->user, 'customer')
                ->post(route('herafy.customer.orders.store'), $this->validPayload([
                    'images' => [$this->fakeImage('rollback.png')],
                ]));

            $this->fail('Expected the simulated persistence failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated image persistence failure.', $exception->getMessage());
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_images', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_only_matching_profession_sees_pending_order(): void
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        $otherProfession = Profession::factory()->create(['status' => 'active']);
        $matching = Craftman::factory()->create(['profession_id' => $profession->id]);
        $different = Craftman::factory()->create(['profession_id' => $otherProfession->id]);
        $order = Order::factory()->create(['profession_id' => $profession->id, 'status' => 'pending']);

        $this->actingAs($matching->user, 'craftman')
            ->get(route('herafy.order.index'))
            ->assertOk()
            ->assertSee($order->title);

        $this->actingAs($different->user, 'craftman')
            ->get(route('herafy.order.index'))
            ->assertOk()
            ->assertDontSee($order->title);
    }

    public function test_craftman_can_open_only_matching_pending_order(): void
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        $otherProfession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $eligible = Order::factory()->create(['profession_id' => $profession->id]);
        $wrongProfession = Order::factory()->create(['profession_id' => $otherProfession->id]);

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.order.show', $eligible))
            ->assertOk();
        $this->get(route('herafy.order.show', $wrongProfession))->assertNotFound();
    }

    public function test_closed_and_soft_deleted_orders_are_unavailable_to_craftman(): void
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);

        foreach (['accepted', 'in_progress', 'completed', 'cancelled'] as $status) {
            $order = Order::factory()->create(['profession_id' => $profession->id, 'status' => $status]);
            $this->actingAs($craftman->user, 'craftman')
                ->get(route('herafy.order.show', $order))
                ->assertNotFound();
        }

        $deleted = Order::factory()->create(['profession_id' => $profession->id]);
        $deleted->delete();
        $this->get(route('herafy.order.show', $deleted->id))->assertNotFound();
    }

    private function validPayload(array $overrides = []): array
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);

        return array_merge([
            'profession_id' => $profession->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
            'title' => 'Repair kitchen cabinets',
            'description' => 'Several cabinet doors need repair and adjustment.',
            'budget' => 250,
            'service_date' => now()->addDay()->toDateString(),
            'address' => 'Main Street',
            'phone' => '0599000000',
        ], $overrides);
    }

    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)
        );
    }
}
