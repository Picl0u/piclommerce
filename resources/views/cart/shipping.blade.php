@extends("piclommerce::layouts.app")

@section("content")
    <div class="order-process">
        <div class="l-container">
            <div class="is-row">
                <div class="is-col is-65 process-container">
                    <div class="timeline check">
                        <h2><i class="fa fa-check"></i> 1.{{ __("piclommerce::web.user_personal_informations") }}</h2>
                    </div>
                    <div class="timeline check">
                        <h2><i class="fa fa-check"></i> 2.{{ __("piclommerce::web.cart_address") }}</h2>
                    </div>
                    <h1>3. {{ __("piclommerce::web.cart_shipping_method") }}</h1>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif

                    <div class="carriers">
                        <form method="post" action="{{ route('cart.user.shipping.store') }}">
                            {{ csrf_field() }}
                            @if(!is_null($carriers))
                                @foreach($carriers as $transport)
                                    <div class="carrier">
                                        <label class="checkbox">
                                            <input type="radio"
                                                   name="carrier_id"
                                                   value="{{ $transport->carriers_id }}"
                                            >
                                            <span class="carrier-infos">
                                                    @if(!empty($transport->Carrier->image))
                                                    <img src="{{ resizeImage($transport->Carrier->image, null, 50) }}"
                                                         alt="{{ $transport->Carrier->name }}"
                                                    >
                                                @endif
                                                <strong>{{ $transport->Carrier->name }}</strong> -
                                                {{ __('piclommerce::web.cart_shipping_delay') }} : {{ $transport->Carrier->delay }} -
                                                    <strong>
                                                        @if(!empty(setting('orders.freeShippingPrice')))
                                                            @if($total >= setting('orders.freeShippingPrice'))
                                                                {{ __('piclommerce::web.cart_free_shipping') }}
                                                            @else
                                                                {{ __('piclommerce::web.cart_price') }} :
                                                                {{ priceFormat($transport->price) }}
                                                            @endif
                                                        @else
                                                            {{ __('piclommerce::web.cart_price') }} :
                                                            {{ priceFormat($transport->price) }}
                                                        @endif
                                                    </strong>
                                                </span>
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <div class="carrier">
                                    <label class="checkbox">
                                        <input type="radio"
                                               name="carrier_id"
                                               value="{{ $carrier->id }}"
                                        >
                                        <span class="carrier-infos">
                                                @if(!empty($carrier->image))
                                                <img src="{{ resizeImage($carrier->image, null, 50) }}"
                                                     alt="{{ $carrier->name }}"
                                                >
                                            @endif
                                            <strong>{{ $carrier->name }}</strong> -
                                            {{ __('piclommerce::web.cart_shipping_delay') }} : {{ $carrier->delay }}  -
                                                <strong>
                                                    @if(!empty(setting('orders.freeShippingPrice')))
                                                        @if($total >= setting('orders.freeShippingPrice'))
                                                            {{ __('piclommerce::web.cart_free_shipping') }}
                                                        @else
                                                            {{ __('piclommerce::web.cart_price') }} :
                                                            {{ priceFormat($carrier->default_price) }}
                                                        @endif
                                                    @else
                                                        {{ __('piclommerce::web.cart_price') }} :
                                                        {{ priceFormat($carrier->default_price) }}
                                                    @endif
                                                </strong>
                                            </span>
                                    </label>
                                </div>
                            @endif

                            <button type="submit">
                                {{ __('piclommerce::web.continue') }}
                            </button>
                        </form>
                    </div>

                    <div class="timeline">
                        <h2>4. {{ __("piclommerce::web.cart_payment") }}</h2>
                    </div>
                </div>
                <div class="is-col is-45">
                    @include('piclommerce::components.cart-resume')
                </div>
            </div>
        </div>
    </div>
@endsection