<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\OrderDetail;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'phone', 'gender', 'address','google_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasBoughtProduct($productId)
    {
        return \Illuminate\Support\Facades\DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->where('orders.user_id', $this->id)
            ->where('orders.status', 'completed')
            ->where('product_variants.product_id', $productId)
            ->exists();
    }

    public function getUnreviewedOrderDetails($productId)
    {
        return \App\Models\OrderDetail::whereHas('order', function($q) {
                $q->where('user_id', $this->id)
                  ->where('status', 'completed');
            })
            ->whereHas('variant', function($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->whereDoesntHave('review')
            ->get();
    }
}