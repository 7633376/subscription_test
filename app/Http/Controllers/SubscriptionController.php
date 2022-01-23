<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SubscribeRequest;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Stripe;

class SubscriptionController extends Controller
{

    public function __construct(){
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function subscribe(SubscribeRequest $request)
    {
        $user          = $request->user();
        $priceId       = $request->get('plan');
        $paymentMethod = $request->get('stripeToken');

        // price id から plan を取得
        $plan = Plan::retrieve($priceId);
        // prod id から product を取得
        $product   = Product::retrieve($plan->product);
        //$localName = $product->metadata->localName;

        $localName = 'default';

        // サブスクリプション開始
        $user->newSubscription($localName, $priceId)->create($paymentMethod);

        return redirect('/home');
    }


    public function cancel(Request $request)
    {
        $user    = $request->user();
        $prodId  = $request->get('prodId');
        $product = Product::retrieve($prodId);

        // サブスクリプションキャンセル
        $user->subscription($product->metadata->localName)->cancel();

        return redirect('/home');
    }

    public function resume(Request $request)
    {
        $user    = $request->user();
        $prodId  = $request->get('prodId');
        $product = Product::retrieve($prodId);

        // // サブスクリプション再開
        $user->subscription($product->metadata->localName)->resume();

        return redirect('/home');
    }
    
}
