<?php

namespace App\Services;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutAddressService
{
    public function addressesForUser(?int $userId): Collection
    {
        if ($userId === null) {
            return new Collection();
        }

        return UserAddress::query()
            ->with(['province', 'district', 'ward'])
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();
    }

    /**
     * @param array<string, mixed> $validated
     * @return array{full_address: string, province_name: string, district_name: string, ward_name: string}
     */
    public function resolve(array $validated): array
    {
        $location = DB::table('wards')
            ->join('districts', 'wards.district_id', '=', 'districts.id')
            ->join('provinces', 'districts.province_id', '=', 'provinces.id')
            ->where('wards.code', $validated['ward_code'])
            ->where('districts.id', $validated['district_id'])
            ->where('provinces.id', $validated['province_id'])
            ->select([
                'wards.name as ward_name',
                'districts.name as district_name',
                'provinces.name as province_name',
            ])
            ->first();

        if (! $location) {
            throw ValidationException::withMessages([
                'ward_code' => 'Địa chỉ giao hàng không hợp lệ.',
            ]);
        }

        return [
            'full_address' => implode(', ', [
                $validated['specific_address'],
                $location->ward_name,
                $location->district_name,
                $location->province_name,
            ]),
            'province_name' => $location->province_name,
            'district_name' => $location->district_name,
            'ward_name' => $location->ward_name,
        ];
    }

    /** @param array<string, mixed> $validated */
    public function saveForUser(?int $userId, array $validated): ?UserAddress
    {
        if ($userId === null || ! ($validated['save_address'] ?? false)) {
            return null;
        }

        return DB::transaction(function () use ($userId, $validated): UserAddress {
            $attributes = [
                'user_id' => $userId,
                'receiver_name' => $validated['shipping_name'],
                'receiver_phone' => $validated['shipping_phone'],
                'province_id' => (int) $validated['province_id'],
                'district_id' => (int) $validated['district_id'],
                'ward_code' => (string) $validated['ward_code'],
                'specific_address' => $validated['specific_address'],
            ];

            $address = UserAddress::query()->firstOrCreate(
                $attributes,
                ['is_default' => false]
            );

            $shouldBeDefault = (bool) ($validated['set_default_address'] ?? false)
                || ! UserAddress::query()->where('user_id', $userId)->where('is_default', true)->exists();

            if ($shouldBeDefault) {
                UserAddress::query()
                    ->where('user_id', $userId)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);

                $address->update(['is_default' => true]);
            }

            return $address->refresh();
        });
    }
}
