@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>{{ __('piclommerce::web.order_cart') }}</h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="cart" data-url="{{ route('cart.product.edit') }}">
        <div class="l-container">
            @include('piclommerce::components.cart')
        </div>
    </div>
@endsection