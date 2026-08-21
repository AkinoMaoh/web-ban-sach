<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'discount_value',
        'max_discount_value',
        'min_order_value',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'is_active',
        'is_public',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_discount_value' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'usage_limit' => 'integer',
            'usage_limit_per_customer' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeCurrentlyValid(Builder $query, ?CarbonInterface $at = null): Builder
    {
        $date = ($at ?? now())->toDateString();

        return $query
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }

    public function scopeWithCapacity(Builder $query): Builder
    {
        return $query->where(function (Builder $capacityQuery): void {
            $capacityQuery
                ->whereNull('usage_limit')
                ->orWhereColumn('used_count', '<', 'usage_limit');
        });
    }

    public function scopeAvailable(Builder $query, ?CarbonInterface $at = null): Builder
    {
        return $query
            ->where('is_active', true)
            ->currentlyValid($at)
            ->withCapacity();
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $searchQuery) use ($keyword): void {
            $searchQuery
                ->where('code', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%");
        });
    }

    public function isAvailableAt(?CarbonInterface $at = null): bool
    {
        $at ??= now();

        if (! $this->is_active || $this->trashed()) {
            return false;
        }

        if ($this->start_date && $at->isBefore($this->start_date->copy()->startOfDay())) {
            return false;
        }

        if ($this->end_date && $at->isAfter($this->end_date->copy()->endOfDay())) {
            return false;
        }

        return $this->usage_limit === null || $this->used_count < $this->usage_limit;
    }

    public function discountAmountFor(float $subtotal): float
    {
        $subtotal = max($subtotal, 0);

        if ($this->type === 'percent') {
            $discount = $subtotal * (float) $this->discount_value / 100;

            if ($this->max_discount_value !== null) {
                $discount = min($discount, (float) $this->max_discount_value);
            }
        } else {
            $discount = (float) $this->discount_value;
        }

        return round(min(max($discount, 0), $subtotal), 2);
    }

    public function remainingUses(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }

        return max($this->usage_limit - $this->used_count, 0);
    }

    public function getStatusCodeAttribute(): string
    {
        $now = now();

        if ($this->trashed()) {
            return 'archived';
        }

        if (! $this->is_active) {
            return 'inactive';
        }

        if ($this->start_date && $now->isBefore($this->start_date->copy()->startOfDay())) {
            return 'upcoming';
        }

        if ($this->end_date && $now->isAfter($this->end_date->copy()->endOfDay())) {
            return 'expired';
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'exhausted';
        }

        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_code) {
            'active' => 'Đang hoạt động',
            'inactive' => 'Đã tắt',
            'upcoming' => 'Chưa bắt đầu',
            'expired' => 'Đã hết hạn',
            'exhausted' => 'Đã hết lượt',
            'archived' => 'Đã lưu trữ',
            default => 'Không xác định',
        };
    }
}
