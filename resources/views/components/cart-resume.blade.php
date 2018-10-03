<div class="table-cart">

    <div class="cart-title">{{ __('piclommerce::web.order_cart') }}</div>

    <div class="is-row table-line">
        @foreach(Cart::instance('shopping')->content() as $row)
            <div class="is-col is-20 cart-image table-row">
                <img src="{{ resizeImage($row->options->image,30,30) }}" alt="{{ $row->name }}">
            </div>
            <div class="is-col is-60 cart-product table-row">
                {{ $row->name }} - {{ $row->id }} - x {{ $row->qty }}<br>
                {{ $row->options->declinaison }}
            </div>
            <div class="is-col is-20 cart-price table-row">
                {{ priceFormat($row->total) }}
            </div>
        @endforeach
    </div>
    <div class="is-row">
        <div class="is-col is-80 text-right tfoot">
            <strong>{{ __('piclommerce::web.cart_sub_total') }} :</strong>
        </div>
        <div class="is-col is-20 tfoot">
            {{ Cart::instance('shopping')->subtotal() }}&euro;
        </div>
    </div>
    <div class="is-row">
        <div class="is-col is-80 text-right tfoot">
            <strong>{{ __('piclommerce::web.cart_vat') }}({{ config('cart.tax') }}%) :</strong>
        </div>
        <div class="is-col is-20 tfoot">
            {{ Cart::instance('shopping')->tax() }}&euro;
        </div>
    </div>

    @php $shipping = priceCarrier(); @endphp
    @php $coupon = checkCoupon(); @endphp
    @if(!empty($coupon))
        <div class="is-row border-top">
            <div class="is-col is-80 text-right tfoot">
                <strong>{{ __('piclommerce::web.cart_coupon_reduction') }}  :</strong>
            </div>
            <div class="is-col is-20 tfoot">
                -{{ priceFormat($coupon['reduceDiff']) }}
            </div>
        </div>
        @php $shipping['total'] -= $coupon['reduceDiff']; @endphp
    @endif

    <div class="is-row">
        <div class="is-col is-80 text-right tfoot">
            <strong>{{ __('piclommerce::web.cart_transport_price') }} :</strong>
        </div>
        <div class="is-col is-20 tfoot">
            {{ priceFormat($shipping['priceCarrier']) }}
        </div>
    </div>
    <div class="is-row">
        <div class="is-col is-80 text-right tfoot">
            <strong>{{ __('piclommerce::web.cart_total') }} :</strong>
        </div>
        <div class="is-col is-20 tfoot">
            {{ priceFormat($shipping['total']) }}
        </div>
    </div>
</div>
