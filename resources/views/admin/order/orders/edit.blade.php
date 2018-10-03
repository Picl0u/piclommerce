@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-shopping-cart"></i>
                    {{ __('piclommerce::admin.navigation_orders') }} / {{ __("piclommerce::admin.navigation_orders") }}
                    <span>{{ __('piclommerce::admin.edit') }} : {{ $order->reference }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <a href="{{ route("admin.orders.orders.index") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <span>{{ __('piclommerce::admin.edit') }} {{ $order->reference }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.orders.orders.index") }}">
                <i class="fa fa-arrow-left"></i>
                {{ __('piclommerce::admin.return') }}
            </a>
            <div class="clear"></div>
        </div>
        <div class="order-title">
            {{ __("piclommerce::web.order") }} : <strong>{{ __("piclommerce::web.order_number") }}{{ $order->id }}</strong>
            - {{ __("piclommerce::web.order_ref") }} <strong>{{ $order->reference }}</strong>
            - {{ __("piclommerce::web.order_created") }} <strong>{{ $order->created_at->format('d/m/Y à H:i') }}</strong>
        </div>

        <div class="is-row">
            <div class="is-col is-70 order-infos">
                <a href="{{ route('admin.orders.orders.invoice',['uuuid' => $order->uuid]) }}" class="invoice-link">
                    <i class="fa fa-file-pdf-o"></i> {{ __("piclommerce::web.order_invoice") }}
                </a>

                <div class="order-status">
                    <div class="title">Status de la commande</div>
                    <div class="status">
                        <div class="label"
                        <?= (!empty($order->Status->color))?' style="background-color:'.$order->Status->color.';color:#FFF"':''; ?>
                        >
                            {{ $order->Status->name }}
                        </div>
                    </div>

                    <hr>
                    <form method="post" action="{{ route('admin.orders.orders.status',['uuid' => $order->uuid]) }}">
                        {{ csrf_field() }}
                        <div class="form-item">
                            <label>{{ __("piclommerce::admin.order_update") }}</label>
                            <div class="append">
                                <select name="status_id">
                                    @foreach($status as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                                <button class="button">{{ __("piclommerce::admin.ok") }}</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>

                <div class="title">{{ __("piclommerce::web.order_history") }}</div>

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

                <div class="title">{{ __("piclommerce::admin.order_carrier") }}</div>
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>
                            {{ __("piclommerce::admin.order_carriers_name") }}
                        </th>
                        <th>
                            {{ __("piclommerce::admin.order_carriers_delay") }}
                        </th>
                        <th>
                            {{ __("piclommerce::admin.order_carrier_price") }}
                        </th>
                        <th>
                            {{ __("piclommerce::admin.order_carrier_id") }}
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $order->shipping_name }}</td>
                        <td>{{ $order->shipping_delay }}</td>
                        <td>{{ priceFormat($order->shipping_price) }}</td>
                        <td>
                            <a href="{{ $order->shipping_url.$order->shipping_order_id }}" target="_blank">
                                {{ $order->shipping_order_id }}
                            </a>
                        </td>
                        <td>
                            <a href="#" data-remodal-target="carrier-order">
                                <i class="fa fa-pencil"></i> {{ __('piclommerce::admin.edit') }}
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="remodal" data-remodal-id="carrier-order">
                    <span data-remodal-action="close" class="remodal-close"></span>


                    <form method="post" action="{{ route('admin.orders.orders.carrier',['uuid' => $order->uuid]) }}">
                        {{ csrf_field() }}

                        <div class="form-item">
                            <label>{{ __("piclommerce::admin.order_carriers_url") }}</label>
                            <input type="text" name="shipping_url" value="{{ $order->shipping_url }}">
                        </div>

                        <div class="form-item">
                            <label>{{ __("piclommerce::admin.order_carrier_id") }}</label>
                            <input type="text" name="shipping_order_id" value="{{ $order->shipping_order_id }}">
                        </div>

                        <div class="form-item">
                            <label>{{ __("piclommerce::admin.order_carriers_delay") }}</label>
                            <input type="text" name="shipping_delay" value="{{ $order->shipping_delay }}">
                        </div>

                        <div class="form-item is-buttons">
                            <button type="submit" class="button">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                {{ __('piclommerce::admin.save') }}
                            </button>
                        </div>

                    </form>

                </div>
                <div class="title">{{ __('piclommerce::admin.order_product_return') }}</div>
            </div>
            <div class="is-col is-30 order-user">
                <div class="title">{{ __('piclommerce::admin.order_user_infos') }}</div>
                <div class="user-infos">
                    <div class="user">
                        {{ __('piclommerce::admin.orders_user_register') }}:
                        @if(empty($order->user_id))
                            <div class="label error">{{ __('piclommerce::admin.no') }}</div>
                        @else
                            <div class="label success">{{ __('piclommerce::admin.yes') }}</div>
                        @endif
                    </div>
                    <div class="user">
                        {{ __('piclommerce::admin.user_email') }} :
                        <a href="mailto:{{ $order->user_email }}" class="label">
                            {{ $order->user_email }}
                        </a>
                    </div>
                    <div class="user">
                        {{ __('piclommerce::admin.user_lastname') }} :
                        <div class="label">
                            {{ $order->user_firstname }} {{ $order->user_lastname }}
                        </div>
                    </div>
                    <div class="user">
                        {{ __('piclommerce::admin.order_validate_count') }} :
                        <div class="label success">
                            {{ $nbOrder }}
                        </div>
                    </div>
                    <div class="user">
                        {{ __('piclommerce::admin.order_total_order') }} :
                        <div class="label success">
                            {{ priceFormat($totalOrder) }}
                        </div>
                    </div>
                    <hr>
                </div>

                <div class="title">{{ __("piclommerce::web.order_delivery_address") }}</div>
                <div class="address">
                    <strong>
                        {{ $order->delivery_gender }}. {{ $order->delivery_firstname }}  {{ $order->delivery_lastname }}
                    </strong><br>
                    {{ $order->delivery_address }}  {{ $order->delivery_additional_address }}<br>
                    {{ $order->delivery_zip_code }} {{ $order->delivery_city }}<br>
                    {{ $order->delivery_country_name }}<br>
                    {{ __("piclommerce::web.order_phone") }} : {{ $order->delivery_phone}}
                </div>
                <div class="title">{{ __("piclommerce::web.order_billing_address") }}</div>
                <div class="address">
                    <strong>
                        {{ $order->billing_gender }}. {{ $order->billing_firstname }}  {{ $order->billing_lastname }}
                    </strong><br>
                    {{ $order->billing_address }}  {{ $order->billing_additional_address }}<br>
                    {{ $order->billing_zip_code }} {{ $order->billing_city }}<br>
                    {{ $order->billing_country_name }}<br>
                    {{ __("piclommerce::web.order_phone") }} : {{ $order->billing_phone}}
                </div>
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
    </div>

@endsection