<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutCartService
{
    /**
     * Ghi nhớ các sản phẩm khách chọn ở trang giỏ hàng rồi dựng snapshot checkout.
     *
     * @return array{display_items: array, order_items: array, subtotal: float}
     */
    public function selectAndSnapshot(?string $requestedItems): array
    {
        if ($requestedItems === null || trim($requestedItems) === '') {
            session()->forget('checkout_item_ids');
        } else {
            session()->put('checkout_item_ids', $this->parseIds($requestedItems));
        }

        return $this->snapshot();
    }

    /**
     * @return array{display_items: array, order_items: array, subtotal: float}
     */
    public function snapshot(): array
    {
        return Auth::check()
            ? $this->authenticatedSnapshot((int) Auth::id())
            : $this->guestSnapshot();
    }

    /**
     * @param array<int, array{product_variant_id: int, price: float, quantity: int}> $orderItems
     */
    public function clearPurchasedItems(array $orderItems): void
    {
        $variantIds = collect($orderItems)
            ->pluck('product_variant_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (Auth::check()) {
            DB::table('carts')
                ->where('user_id', Auth::id())
                ->whereIn('product_variant_id', $variantIds)
                ->delete();
        } else {
            $sessionCart = session()->get('cart', []);

            foreach ($variantIds as $variantId) {
                unset($sessionCart[$variantId]);
            }

            session()->put('cart', $sessionCart);
        }

        session()->forget('checkout_item_ids');
    }

    private function authenticatedSnapshot(int $userId): array
    {
        $query = DB::table('carts')
            ->join('product_variants', 'carts.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('carts.user_id', $userId)
            ->select([
                'carts.id as cart_id',
                'carts.quantity',
                'product_variants.id as variant_id',
                'product_variants.product_id',
                'product_variants.price',
                'product_variants.sale_price',
                'products.name as product_name',
            ]);

        $selectedIds = session()->get('checkout_item_ids');

        if (is_array($selectedIds) && $selectedIds !== []) {
            $query->whereIn('carts.id', $selectedIds);
        }

        return $this->buildSnapshot($query->get(), 'cart_id');
    }

    private function guestSnapshot(): array
    {
        $sessionCart = session()->get('cart', []);

        if ($sessionCart === []) {
            return $this->emptySnapshot();
        }

        $variantIds = array_map('intval', array_keys($sessionCart));
        $selectedIds = session()->get('checkout_item_ids');

        if (is_array($selectedIds) && $selectedIds !== []) {
            $variantIds = array_values(array_intersect($variantIds, array_map('intval', $selectedIds)));
        }

        if ($variantIds === []) {
            return $this->emptySnapshot();
        }

        $items = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->whereIn('product_variants.id', $variantIds)
            ->select([
                'product_variants.id as variant_id',
                'product_variants.product_id',
                'product_variants.price',
                'product_variants.sale_price',
                'products.name as product_name',
            ])
            ->get()
            ->map(function ($item) use ($sessionCart) {
                $item->quantity = max((int) ($sessionCart[$item->variant_id]['quantity'] ?? 1), 1);

                return $item;
            });

        return $this->buildSnapshot($items, 'variant_id');
    }

    private function buildSnapshot($items, string $displayKey): array
    {
        $displayItems = [];
        $orderItems = [];
        $subtotal = 0.0;

        foreach ($items as $item) {
            $regularPrice = (float) $item->price;
            $salePrice = (float) ($item->sale_price ?? 0);
            $unitPrice = $salePrice > 0 && $salePrice < $regularPrice ? $salePrice : $regularPrice;
            $quantity = max((int) $item->quantity, 1);
            $subtotal += $unitPrice * $quantity;

            $displayItems[(int) $item->{$displayKey}] = [
                'name' => $item->product_name,
                'price' => $unitPrice,
                'original_price' => $regularPrice,
                'quantity' => $quantity,
                'variant_id' => (int) $item->variant_id,
            ];

            $orderItems[] = [
                'product_variant_id' => (int) $item->variant_id,
                'price' => $unitPrice,
                'quantity' => $quantity,
            ];
        }

        return [
            'display_items' => $displayItems,
            'order_items' => $orderItems,
            'subtotal' => round($subtotal, 2),
        ];
    }

    private function emptySnapshot(): array
    {
        return ['display_items' => [], 'order_items' => [], 'subtotal' => 0.0];
    }

    /** @return array<int, int> */
    private function parseIds(string $items): array
    {
        return collect(explode(',', $items))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
