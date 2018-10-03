@if(Cart::instance('shopping')->total() > 0)
    <div class="table-cart">
        <div class="is-row is-hidden-mobile">
            <div class="is-col is-30 thead">{{ __('piclommerce::web.cart_product') }}</div>
            <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_unit_price') }}</div>
            <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_quantity') }}</div>
            <div class="is-col is-20 thead">{{ __('piclommerce::web.cart_total') }}</div>
            <div class="is-col is-10 thead"></div>
        </div>
        @foreach(Cart::instance('shopping')->content() as $row)
            <div class="is-row is-bricks border-top">
                <div class="is-col is-30 tbody">
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
                <div class="is-col is-20 tbody" data-id="{{ $row->rowId }}">
                    <div class="is-shown-mobile">
                        <strong>{{ __('piclommerce::web.cart_quantity') }}</strong>
                        <div class="clear"></div>
                    </div>
                    <div class="quantity-cart">
                        <div class="less">-</div>
                        <div class="qty">{{ $row->qty }}</div>
                        <div class="more">+</div>
                    </div>

                </div>
                <div class="is-col is-20 tbody">
                    <strong class="is-shown-mobile">{{ __('piclommerce::web.cart_total') }}</strong>
                    <div class="product-total">{{ priceFormat($row->total) }}</div>
                </div>
                <div class="is-col is-10 tbody" data-id="{{ $row->rowId }}">
                    <span class="delete-product">
                        <i class="fa fa-trash"></i>
                    </span>
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

        <div class="is-row coupon-cart border-top">
            <div class="is-col is-80 text-right tfoot">
                <strong>
                    {{ __('piclommerce::web.cart_coupon') }} :
                </strong>
            </div>
            <div class="is-col is-20 tfoot">
                <form method="post" action="{{ route("cart.coupon") }}">
                    {{ csrf_field() }}
                    <div class="form-item">
                        <div class="is-append">
                            <input type="text"
                                   name="coupon"
                                   value="{{ (!empty($coupon))?$coupon['name']:'' }}"
                                   required="required"
                            >
                            <button class="button outline"><i class="fa fa-check"></i></button>
                        </div>
                    </div>
                </form>
                @if(!empty($coupon))
                    <a href="{{ route('cart.coupon.cancel') }}" class="small">{{ __('piclommerce::web.cancel') }}</a>
                @endif
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
                {{ priceFormat($shippingPrice) }}
            </div>
        </div>

        <div class="is-row border-top">
            <div class="is-col is-80 text-right tfoot">
                <strong>{{ __('piclommerce::web.cart_total') }} :</strong>
            </div>
            <div class="is-col is-20 tfoot">
                {{ priceFormat($total) }}
            </div>
        </div>

        <div class="cart-links">
            <a href="/">
                <i class="fa fa-arrow-left"></i>
                {{ __('piclommerce::web.cart_continue_shopping') }}
            </a>
            <div>
                <a href="{{ route('cart.user.connect') }}">
                    <i class="fa fa-check"></i>
                    {{ __('piclommerce::web.cart_order') }}
                </a>
                @if(!empty(setting('orders.freeShippingPrice')))
                    @if(Cart::instance('shopping')->total(2,".","") < setting('orders.freeShippingPrice'))
                        <p>
                            {{ __('piclommerce::web.cart_again') }}
                            <strong>
                                {{ priceFormat(setting('orders.freeShippingPrice') - Cart::instance('shopping')->total()) }}
                            </strong>
                            {{ __('piclommerce::web.cart_benefit') }}
                        </p>
                    @endif
                @endif
            </div>
        </div>

    </div>
@else
    <h2>{{ __('piclommerce::web.cart_empty') }}</h2>
    <div class="cart-links">
        <a href="/">
            <i class="fa fa-arrow-left"></i>
            {{ __('piclommerce::web.cart_continue_shopping') }}
        </a>
    </div>
@endif