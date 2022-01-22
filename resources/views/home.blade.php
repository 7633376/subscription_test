@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card col-md-8">
            <form action="/subscribe" method="post" id="payment-form">
                @csrf

                {{-- 商品情報 --}}
                <div class="form-group">
                    <label>サブスクリプション商品:</label>
                    <select id="plan" name="plan" class="form-control">
                        <option value="Stripeの商品ページにあるAPI_ID">テストプレミアムプラン</option>
                    </select>
                </div>

                {{-- カード情報 --}}
                <div class="form-group">
                    <label for="card-holder-name">支払い情報:</label>
                    <div>
                        <input id="card-holder-name" class="form-control" type="text" placeholder="カード名義人">
                    </div>
                    <div id="card-element" class="w-100">
                    <!-- A Stripe Element will be inserted here. -->
                    </div>

                    <!-- Used to display form errors. -->
                    <div id="card-errors" role="alert"></div>
                </div>
                <input type="hidden" id="stripeToken" name="stripeToken">

                <div id="card-button" class="btn btn-primary mt-5" data-secret="{{ $intent->client_secret }}">Submit Payment</div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- jquery --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
{{-- stripe.js --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
    $(function() {
        // Create a Stripe client.
        var stripe = Stripe('.envに追加した公開可能キー');

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
            color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
        };

        // Create an instance of the card Element.
        var cardElement = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        cardElement.mount('#card-element');

        const cardHolderName = $("#card-holder-name");
        const cardButton     = $("#card-button");
        const clientSecret   = cardButton.data('secret');

        cardButton.on('click', async (e) => {
            cardButton.prop('disabled', true);
            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: cardHolderName.value }
                    }
                }
            );

            if (error) {
                // ユーザーに"error.message"を表示する…
                cardButton.prop('disabled', false);
            } else {
                // カードの検証に成功した…
                cardButton.prop('disabled', false);

                // 支払い方法識別子
                var form = $('#payment-form');
                var hiddenInput = $("#stripeToken");
                hiddenInput.attr('value', setupIntent.payment_method);

                form.submit();
            }
        });
    })
</script>

<style>
.StripeElement {
  box-sizing: border-box;

  height: 40px;

  padding: 10px 12px;

  border: 1px solid transparent;
  border-radius: 4px;
  background-color: white;

  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>
