<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Price;
use Stripe\Stripe;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->middleware('auth');
    }

    // /**
    //  * Show the application dashboard.
    //  *
    //  * @return \Illuminate\Contracts\Support\Renderable
    //  */
    // public function index()
    // {
    //     return view('home');
    // }

    public function index()
    {
        $user = Auth::user();

        return view('home', [
            'intent'       => $user->createSetupIntent(),
            // 現在のユーザーに紐づいているサブスクリプション
            'userProducts' => $user->products(),
            // dashboardで作成されているサブスクリプション全件
            'products'     => Price::getAll(),
        ]);
    }
}
