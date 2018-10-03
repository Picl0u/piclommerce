@extends("piclommerce::layouts.app")

@section("content")
<div class="head-title">
    <div class="l-container">
        <h1>
            {{ __('piclommerce::web.user_my_account') }}
        </h1>
    </div>
</div>
@include('piclommerce::components.search-bar')
<div class="account-section">

    <div class="l-container">

        <div class="sidebar is-shown-mobile">
            @include("piclommerce::components.users.sidebar")
        </div>

        <div class="is-row is-hidden-mobile">
            <div class="is-col is-25 account-link">
                <a href="{{ route('user.infos') }}">
                    <i class="fa fa-user"></i>
                    {{ __('piclommerce::web.user_my_informations') }}
                </a>
            </div>
            <div class="is-col is-25 account-link">
                <a href="{{ route('user.addresses') }}">
                    <i class="fa fa-map-marker"></i>
                    {{ __('piclommerce::web.user_my_addresses') }}
                </a>
            </div>
            <div class="is-col is-25 account-link">
                <a href="{{ route('order.index') }}">
                    <i class="fa fa-shopping-bag"></i>
                    {{ __('piclommerce::web.user_my_orders') }}
                </a>
            </div>
            <div class="is-col is-25 account-link">
                <a href="{{ route('whishlist.index') }}">
                    <i class="fa fa-heart"></i>
                    {{ __('piclommerce::web.user_my_whishlist') }}
                </a>
            </div>
        </div>

    </div>
</div>
@endsection