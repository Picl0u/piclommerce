@extends("piclommerce::layouts.app")

@section("content")
    <div class="order-process">
        <div class="l-container">
            <div class="is-row">
                <div class="is-col is-65 process-container">
                    <div class="timeline check">
                        <h2><i class="fa fa-check"></i> 1.{{ __("piclommerce::web.user_personal_informations") }}</h2>
                    </div>
                    <h1>2. {{ __("piclommerce::web.cart_address") }}</h1>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif

                    <div class="order-addresses">
                        @if(!empty($addressList))
                            <form method="post" action="{{ route("cart.user.address.select") }}">
                                {{ csrf_field() }}
                                <div class="addresses-choose delivery-address">
                                    <div class="title">
                                        <h2>{{ __('piclommerce::web.user_delivery_address') }}</h2>
                                    </div>
                                    <div class="is-row is-bricks">
                                        <?php $i = 0; ?>
                                        @foreach($addressList as $address)
                                            @if(!empty($address['delivery']))
                                                <div class="is-col is-33 form-item">
                                                    <label class="checkbox">
                                                        <input type="radio"
                                                           name="delivery_address"
                                                           value="{{ $address['uuid'] }}"
                                                            {{ (empty($i)) ? 'checked' : '' }}
                                                        >
                                                        <strong>
                                                            {{ $address['firstname'] }} {{ $address['lastname'] }}
                                                        </strong><br>
                                                        {{ $address['address'] }}<br>
                                                        @if(!empty($address['additional_address']))
                                                            {{ $address['additional_address'] }}<br>
                                                        @endif
                                                        {{ $address['zip_code'] }} -
                                                        {{ $address['city'] }}<br>
                                                        {{ __('piclommerce::web.user_phone') }} : {{ $address['phone'] }}<br>
                                                    </label>
                                                </div>
                                                <?php $i++; ?>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
    
                                <div class="addresses-choose delivery-address">
                                    <div class="title">
                                        <h2>{{ __('piclommerce::web.user_billing_addresses') }}</h2>
                                    </div>
                                    <div class="is-row is-bricks">
                                        <?php $i = 0; ?>
                                        @foreach($addressList as $key => $address)
                                            @if(!empty($address['billing']))
                                                <div class="is-col col-33 form-item">
                                                    <label class="checkbox">
                                                        <input type="radio"
                                                               name="billing_address"
                                                               value="{{ $address['uuid'] }}"
                                                                {{ (empty($i)) ? 'checked' : '' }}
                                                        >
                                                        <strong>
                                                            {{ $address['firstname'] }} {{ $address['lastname'] }}
                                                        </strong><br>
                                                        {{ $address['address'] }}<br>
                                                        @if(!empty($address['additional_address']))
                                                            {{ $address['additional_address'] }}<br>
                                                        @endif
                                                        {{ $address['zip_code'] }} -
                                                        {{ $address['city'] }}<br>
                                                        {{ __('piclommerce::web.user_phone') }} : {{ $address['phone'] }}<br>
                                                    </label>
                                                </div>
                                                <?php $i++; ?>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
    
                                <button type="submit">
                                    {{ __('piclommerce::web.continue') }}
                                </button>
                                
                            </form>

                            <div class="new-address-control">
                                <a href="#">
                                    <i class="fa fa-plus"></i>
                                    {{ __('piclommerce::web.user_add_new_address') }}
                                </a>
                            </div>
                            <div class="add-new-address">
                                @include('piclommerce::cart.form-adress')
                            </div>
                        @else
                            @include('piclommerce::cart.form-adress')
                        @endif
                    </div>



                </div>
                <div class="is-col is-45">
                    @include('piclommerce::components.cart-resume')
                </div>
            </div>
        </div>
    </div>
@endsection