<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Cashier\Subscription;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }

    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user         = $this->user();
            $priceId      = $this->get('plan');
            $subscription = Subscription::whereStripePlan($priceId);

            $isSubscribed = $subscription->count()
                ? $user->subscribed($subscription->first()->name)
                : false;
            $isCancelled = $subscription->count()
                ? $user->subscription($subscription->first()->name)->cancelled()
                : false;

            if ($isSubscribed) {
                if ($isCancelled) {
                    $validator->errors()->add('is_cancelled', "選択された商品は、キャンセル中です。再開ボタンを利用してください");
                } else {
                    $validator->errors()->add('is_subscribed', '選択された商品は、定期課金中です');
                }
            }
        });
    }
}
