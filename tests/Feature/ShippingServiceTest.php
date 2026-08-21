<?php

namespace Tests\Feature;

use App\Services\ShippingService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    private ShippingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('weight_grams')->default(500);
        });

        config([
            'services.ghn.base_url' => 'https://ghn.test/v2',
            'services.ghn.token' => 'test-token',
            'services.ghn.shop_id' => '12345',
            'services.ghn.store_district_id' => 100,
            'services.ghn.service_type_id' => 2,
            'services.ghn.default_item_weight' => 500,
            'services.ghn.quote_ttl_minutes' => 15,
        ]);

        $this->service = app(ShippingService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('product_variants');
        parent::tearDown();
    }

    public function test_quote_uses_real_cart_weight_and_can_be_verified(): void
    {
        Schema::getConnection()->table('product_variants')->insert([
            ['id' => 10, 'weight_grams' => 350],
            ['id' => 20, 'weight_grams' => 800],
        ]);
        Http::fake(['*' => Http::response(['data' => ['total' => 42000]], 200)]);
        $items = [
            ['product_variant_id' => 10, 'quantity' => 2, 'price' => 100000],
            ['product_variant_id' => 20, 'quantity' => 1, 'price' => 150000],
        ];

        $quote = $this->service->quote(1, 2, '00001', $items);

        $this->assertSame(1500, $quote['weight']);
        $this->assertSame(42000.0, $quote['fee']);
        $this->assertNotSame('', $quote['quote_token']);
        Http::assertSent(fn (Request $request): bool => $request['weight'] === 1500);

        $verified = $this->service->verifyQuote($quote['quote_token'], 1, 2, '00001', $items);
        $this->assertSame(42000.0, $verified['fee']);
        $this->assertSame(1500, $verified['weight']);
    }

    public function test_quote_rejects_a_changed_cart(): void
    {
        Schema::getConnection()->table('product_variants')->insert([
            'id' => 10,
            'weight_grams' => 500,
        ]);
        Http::fake(['*' => Http::response(['data' => ['total' => 30000]], 200)]);
        $items = [['product_variant_id' => 10, 'quantity' => 1, 'price' => 100000]];
        $quote = $this->service->quote(1, 2, '00001', $items);

        $this->expectException(ValidationException::class);
        $this->service->verifyQuote(
            $quote['quote_token'],
            1,
            2,
            '00001',
            [['product_variant_id' => 10, 'quantity' => 2, 'price' => 100000]]
        );
    }
}
