<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Laravel\Cashier\Billable;
use Stripe\Plan;
use Stripe\Product;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


        /**
     * ユーザーに紐づいているサブスクリプションを返す
     */
    public function products() {
        $products = [];
        foreach ($this->subscriptions()->get() as $subscription) {
            $priceId = $subscription->stripe_plan;

            // price id から plan を取得
            $plan = Plan::retrieve($priceId);
            // prod id から product を取得
            $product =Product::retrieve($plan->product);

            // dashboardで設定したメタデータを取得
            //$localName           = $product->metadata->localName;
            //$localName = $product->name;
            $localName = 'default';
            $product->cancelled  = $this->subscription($localName)->cancelled();

            $products[] = $product;
        }

        return $products;
    }
}
