@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.user_my_orders') }}
            </h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="account-section">
        <div class="l-container">

            <div class="is-row">
                <div class="is-col is-20 sidebar">
                    @include("piclommerce::components.users.sidebar")
                </div>
                <div class="is-col is-80">
                    <div class="title">
                        <h2>
                            {{ __("piclommerce::web.order") }} : {{ $order->reference }}
                            <a href="{{ route('order.index') }}">
                                <i class="fa fa-chevron-left"></i>
                                {{ __('piclommerce::web.return') }}
                            </a>
                        </h2>
                    </div>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif
                    <div class="order">
                        <div class="order-title">
                            {{ __("piclommerce::web.order") }} : <strong>{{ __("piclommerce::web.order_number") }}{{ $order->id }}</strong>
                            - {{ __("piclommerce::web.order_ref") }} <strong>{{ $order->reference }}</strong>
                            - {{ __("piclommerce::web.order_created") }} <strong>{{ $order->created_at->format('d/m/Y à H:i') }}</strong>
                        </div>

                        <a href="{{ route('order.invoice',['uuid' => $order->uuid]) }}" class="invoice-link">
                            <i class="fa fa-file-pdf-o"></i> {{ __("piclommerce::web.order_invoice") }}
                        </a>

                        <div class="title">
                            <h2>{{ __("piclommerce::web.order_history") }}</h2>
                        </div>

                        <div class="order-history">
                            @foreach($order->OrdersStatus as $history)
                                <div class="history">
                                    <div class="label"
                                    <?= (!empty($history->Status->color))?' style="background-color:'.$history->Status->color.';color:#FFF"':''; ?>
                                    >
                                        {{ $history->Status->name }}
                                    </div>
                                    <div class="date">
                                        {{ __("piclommerce::web.the") }} {{ $history->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="carrier">
                            <div class="title">
                                <h3>{{ __("piclommerce::web.order_carrier") }}</h3>
                            </div>
                            <div class="carrier-infos">
                                {{ $order->shipping_name }} -
                                {{ __("piclommerce::web.order_delay") }} : {{ $order->shipping_delay }}
                                @if(!empty($order->shipping_order_id))
                                    - <a href="{{ $order->shipping_url.$order->shipping_order_id }}" target="_blank">
                                        {{ __("piclommerce::web.order_shipping_id") }} : {{ $order->shipping_order_id }}
                                    </a>
                                @endif
                            </div>

                        </div>

                        <div class="is-row">
                            <div class="is-col is-50 address">
                                <div class="title">
                                    <h3>{{ __("piclommerce::web.order_delivery_address") }}</h3>
                                </div>
                                <strong>
                                    {{ $order->delivery_gender }} {{ $order->delivery_firstname }}  {{ $order->delivery_lastname }}
                                </strong><br>
                                {{ $order->delivery_address }}  {{ $order->delivery_additional_address }}<br>
                                {{ $order->delivery_zip_code }} {{ $order->delivery_city }}<br>
                                {{ $order->delivery_country_name }}<br>
                                {{ __("piclommerce::web.order_phone") }} : {{ $order->delivery_phone}}
                            </div>
                            <div class="is-col is-50 address">
                                <div class="title">
                                    <h3>{{ __("piclommerce::web.order_billing_address") }}</h3>
                                </div>
                                <strong>
                                    {{ $order->billing_gender }} {{ $order->billing_firstname }}  {{ $order->billing_lastname }}
                                </strong><br>
                                {{ $order->billing_address }}  {{ $order->billing_additional_address }}<br>
                                {{ $order->billing_zip_code }} {{ $order->billing_city }}<br>
                                {{ $order->billing_country_name }}<br>
                                {{ __("piclommerce::web.order_phone") }} : {{ $order->billing_phone}}
                            </div>
                        </div>

                        <div class="order-detail">
                            <div class="cart">
                                <div class="title">
                                    <h4>{{ __("piclommerce::web.order_detail") }}</h4>
                                </div>
                                <div class="is-row is-hidden-mobile">
                                    <div class="is-col is-40 thead">{{ __('piclommerce::web.cart_product') }}</div>
                                    <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_unit_price') }}</div>
                                    <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_quantity') }}</div>
                                    <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_total') }}</div>
                                </div>
                                @foreach($order->OrdersProducts as $product)
                                    <div class="is-row is-bricks border-top">
                                        <div class="is-col is-40 tbody">
                                            <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_product') }}</strong>
                                            <div class="is-row">
                                                <div class="is-col is-25">
                                                    <img src="{{ resizeImage($product->getMedias('image')['target_path'],50,50) }}"
                                                         alt="{{ $product->name }}"
                                                    >
                                                </div>
                                                <div class="is-col is-75">
                                                    <div class="product-name">{{ $product->name }}</div>
                                                    <div class="product-ref">
                                                        {{ __("piclommerce::web.order_ref") }} : {{ $product->ref }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="is-col is-20 tbody">
                                            <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_unit_price') }}</strong>
                                            <div class="product-price">{{ priceFormat($product->price_ht) }}</div>
                                        </div>
                                        <div class="is-col is-20 tbody">
                                            <div class="is-shown-mobile">
                                                <strong>{{ __('piclommerce::web.cart_quantity') }}</strong>
                                            </div>
                                            {{ $product->quantity }}

                                        </div>
                                        <div class="is-col is-20 tbody">
                                            <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_total') }}</strong>
                                            <div class="product-total">{{ priceFormat($product->price_ttc) }}</div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="is-row border-top">
                                    <div class="is-col is-80 text-right tfoot">
                                        <strong>{{ __('piclommerce::web.cart_sub_total') }} :</strong>
                                    </div>
                                    <div class="is-col is-20 tfoot">{{ priceFormat($order->price_ht) }}</div>
                                </div>

                                <div class="is-row border-top">
                                    <div class="is-col is-80 text-right tfoot">
                                        <strong>{{ __('piclommerce::web.cart_vat') }}({{ config('cart.tax') }}%) :</strong>
                                    </div>
                                    <div class="is-col is-20 tfoot">{{ priceFormat($order->vat_price) }}</div>
                                </div>
                                @if(!is_null($order->coupon_price) && !empty($order->coupon_price))
                                    <div class="is-row border-top">
                                        <div class="is-col is-80 text-right tfoot">
                                            <strong>{{ __('piclommerce::web.cart_coupon_reduction') }}  ({{ $order->coupon_name }}) :</strong>
                                        </div>
                                        <div class="is-col is-20 tfoot">-{{ priceFormat($order->coupon_price) }}</div>
                                    </div>
                                @endif
                                <div class="is-row border-top">
                                    <div class="is-col is-80 text-right tfoot">
                                        <strong>{{ __('piclommerce::web.cart_transport_price') }} :</strong>
                                    </div>
                                    <div class="is-col is-20 tfoot">
                                        {{ priceFormat($order->shipping_price) }}
                                    </div>
                                </div>
                                <div class="is-row border-top">
                                    <div class="is-col is-80 text-right tfoot">
                                        <strong>{{ __('piclommerce::web.cart_total') }} :</strong>
                                    </div>
                                    <div class="is-col is-20 tfoot">
                                        {{ priceFormat($order->price_ttc) }}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="order-return">
                            <div class="title">
                                <h5>{{ __("piclommerce::web.order_return") }}</h5>
                            </div>
                            <form method="post" action="{{ route("order.return",['uuid' => $order->uuid]) }}">

                                <div class="form-item">
                                    @foreach($order->OrdersProducts as $product)
                                        <label class="checkbox">
                                            <input type="checkbox" name="product[]" value="{{ $product->id }}">
                                            {{ $product->ref }} - {{ $product->name }}
                                        </label>
                                    @endforeach
                                </div>

                                <div class="form-item">
                                    <label>{{ __('piclommerce::web.order_return_message') }}</label>
                                    <textarea cols="0" rows="0" name="message"></textarea>
                                </div>

                                <button type="submit">
                                    {{ __("piclommerce::web.order_return_send") }}
                                </button>

                            </form>
                        </div>

                        <div class="order-return-history">
                            <div class="title">
                                <h5>{{ __("piclommerce::web.order_return_history") }}</h5>
                            </div>
                            @foreach($order->OrdersReturns as $return)
                                <div class="return">
                                    <div class="date">
                                        {{ __("piclommerce::web.order_return_at") }}
                                        {{ $return->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                    <p>
                                        {{ $return->message }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection