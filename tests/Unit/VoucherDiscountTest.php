<?php

namespace Tests\Unit;

use App\Models\Voucher;
use PHPUnit\Framework\TestCase;

class VoucherDiscountTest extends TestCase
{
    public function test_fixed_discount_never_makes_subtotal_negative(): void
    {
        $voucher = new Voucher([
            'type' => 'fixed',
            'discount_value' => 150000,
        ]);

        $this->assertSame(100000.0, $voucher->discountAmountFor(100000));
    }

    public function test_percent_discount_respects_maximum_discount(): void
    {
        $voucher = new Voucher([
            'type' => 'percent',
            'discount_value' => 20,
            'max_discount_value' => 50000,
        ]);

        $this->assertSame(50000.0, $voucher->discountAmountFor(400000));
    }

    public function test_percent_discount_uses_exact_rate_below_cap(): void
    {
        $voucher = new Voucher([
            'type' => 'percent',
            'discount_value' => 10,
            'max_discount_value' => 50000,
        ]);

        $this->assertSame(20000.0, $voucher->discountAmountFor(200000));
    }
}
