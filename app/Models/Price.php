<?php

namespace App\Models;

use Stripe\Product;
use Stripe\Price as StripePrice;

class Price extends StripePrice
{
    /**
     * price に紐づくproduct name を付加して全件返す
     */
    public static function getAll()
    {
        $retPrices = [];
        foreach (StripePrice::all() as $price) {
            $product = Product::retrieve($price->product);

            // price に紐づく product name を付加
            $price->productName = $product->name;

            $retPrices[] = $price;
        }

        return $retPrices;
    }
}