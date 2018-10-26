@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>{{ __('piclommerce::web.cart_recap') }}</h1>
        </div>
    </div>
    <div class="cart">
        <div class="l-container">
            <div class="is-row">
                <div class="is-col is-50">
                    <div class="title">
                        <h1>{{ __('piclommerce::web.user_delivery_address') }}</h1>
                    </div>
                    <strong>
                        {{ $address['delivery']['firstname'] }} {{ $address['delivery']['lastname'] }}
                    </strong><br>
                    {{ $address['delivery']['address'] }}<br>
                    @if(!empty($address['delivery']['additional_address'] ))
                        {{ $address['delivery']['additional_address'] }}<br>
                    @endif
                    {{ $address['delivery']['zip_code'] }} -
                    {{ $address['delivery']['city'] }} -
                    {{ $address['delivery']['country_name'] }}<br>
                    {{ __('piclommerce::web.user_phone') }} : {{ $address['billing']['phone'] }}
                </div>
                <div class="is-col is-50">
                    <div class="title">
                        <h1>{{ __('piclommerce::web.user_billing_addresses') }}</h1>
                    </div>
                    <strong>
                        {{ $address['billing']['firstname'] }} {{ $address['billing']['lastname'] }}
                    </strong><br>
                    {{ $address['billing']['address'] }}<br>
                    @if(!empty($address['billing']['additional_address'] ))
                        {{ $address['billing']['additional_address'] }}<br>
                    @endif
                    {{ $address['billing']['zip_code'] }} -
                    {{ $address['billing']['city'] }} -
                    {{ $address['billing']['country_name'] }}<br>
                    {{ __('piclommerce::web.user_phone') }} : {{ $address['billing']['phone'] }}
                </div>
            </div>

            <div class="table-cart recap-table">
                <div class="is-row is-hidden-mobile">
                    <div class="is-col is-40 thead">{{ __('piclommerce::web.cart_product') }}</div>
                    <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_unit_price') }}</div>
                    <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_quantity') }}</div>
                    <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_total') }}</div>
                </div>
                @foreach(Cart::instance('shopping')->content() as $row)
                    <div class="is-row is-bricks border-top">
                        <div class="is-col is-40 tbody">
                            <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_product') }}</strong>
                            <div class="is-row">
                                <div class="is-col is-25">
                                    <img src="{{ resizeImage($row->options->image,50,50) }}"
                                         alt="{{ $row->name }}"
                                    >
                                </div>
                                <div class="is-col is-75">
                                    <div class="product-name">{{ $row->name }}</div>
                                    <div class="product-ref">Référence : {{ $row->id }}</div>
                                    <div class="product-declinaisons">{{ $row->options->declinaison }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="is-col is-20 tbody">
                            <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_unit_price') }}</strong>
                            <div class="product-price">{{ priceFormat($row->price) }}</div>
                        </div>
                        <div class="is-col is-20 tbody">
                            <div class="is-shown-mobile">
                                <strong>{{ __('piclommerce::web.cart_quantity') }}</strong>
                                <div class="clear"></div>
                            </div>
                            {{ $row->qty }}

                        </div>
                        <div class="is-col is-20 tbody">
                            <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_total') }}</strong>
                            <div class="product-total">{{ priceFormat($row->total) }}</div>
                        </div>
                    </div>
                @endforeach
                <div class="is-row border-top">
                    <div class="is-col is-80 text-right tfoot">
                        <strong>{{ __('piclommerce::web.cart_sub_total') }} :</strong>
                    </div>
                    <div class="is-col is-20 tfoot">
                        {{ Cart::instance('shopping')->subtotal() }}&euro;
                    </div>
                </div>
                <div class="is-row border-top">
                    <div class="is-col is-80 text-right tfoot">
                        <strong>{{ __('piclommerce::web.cart_vat') }}({{ config('cart.tax') }}%) :</strong>
                    </div>
                    <div class="is-col is-20  tfoot">
                        {{ Cart::instance('shopping')->tax() }}&euro;
                    </div>
                </div>

                @if(!empty($coupon))
                    <div class="is-row border-top">
                        <div class="is-col is-80 text-right tfoot">
                            <strong>{{ __('piclommerce::web.cart_coupon_reduction') }} :</strong>
                        </div>
                        <div class="is-col is-20 tfoot">
                            -{{ priceFormat($coupon['reduceDiff']) }}
                        </div>
                    </div>
                @endif

                <div class="is-row border-top">
                    <div class="is-col is-80 text-right tfoot">
                        <strong>{{ __('piclommerce::web.cart_transport_price') }} :</strong>
                    </div>
                    <div class="is-col is-20 tfoot">
                        {{ priceFormat($shipping['price']) }}
                    </div>
                </div>

                <div class="is-row border-top">
                    <div class="is-col is-80 text-right tfoot">
                        <strong>{{ __('piclommerce::web.cart_total') }} :</strong>
                    </div>
                    <div class="is-col is-20 tfoot">
                        {{ priceFormat($total + $shipping['price']) }}
                    </div>
                </div>

                @if(!empty(setting('orders.cgvId')))
                    <div class="cgv">
                        {{ __('piclommerce::web.cart_process_cgv') }}
                        <a href="#cgv-modal">{{ $cgv->name }}</a>
                    </div>
                @endif

                <div class="is-row">
                    @if(setting("paypal.enable"))
                        <div class="is-col">
                            <div class="payment-button">
                                <a href="{{ route('cart.process') }}">
                                    {{ __('piclommerce::web.cart_pay_order') }}
                                </a>
                                <p>Paiement sécurisé avec Paypal</p>
                                <img src="/images/credit-card-icons-png.png" alt="Moyens de paiements">
                            </div>
                        </div>
                    @endif
                    @if(setting("stripe.enable"))
                        <div class="is-col">
                            <div class="payment-button">
                                <form method="post" action="{{ route("cart.process.stripe") }}">
                                    {{ csrf_field() }}
                                    <script
                                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                            data-key="{{ setting("stripe.pubKey") }}"
                                            data-amount="{{ ($total + $shipping['price'])*100 }}"
                                            data-name="{{ setting("generals.websiteName") }}"
                                            data-description="{{ __('piclommerce::web.cart_pay_order') }}"
                                            data-image="{{ asset(setting('generals.logo')) }}"
                                            data-locale="{{ config("app.locale") }}"
                                            data-label="{{ __('piclommerce::web.cart_pay_order') }}"
                                            data-email="{{ Auth::user()->email }}"
                                            data-currency="eur">
                                    </script>
                                </form>
                                <p>Paiement sécurisé avec Stripe</p>
                                <img src="/images/credit-card-icons-png.png" alt="Moyens de paiements">
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
    @if(!empty(setting('orders.cgvId')))
        <div class="remodal" data-remodal-id="cgv-modal">
            <div data-remodal-action="close" class="remodal-close"></div>
            <div class="cgv-content">
                <h3>{{ $cgv->name }}</h3>
                {!! $cgv->description !!}
            </div>
        </div>
    @endif
@endsection