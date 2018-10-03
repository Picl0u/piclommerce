@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.user_my_whishlist') }}
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
                        <h2>{{ __('piclommerce::web.user_my_whishlist') }}</h2>
                    </div>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif

                    <div class="whishlist">
                        @if(Cart::instance('whishlist')->total() > 0)
                            <div class="is-row">
                                @foreach(Cart::instance('whishlist')->content() as $row)
                                    <div class="is-col is-25">
                                        <div class="product">
                                            @if($row->options->image)
                                                <div class="product-image">
                                                    <img src="{{ resizeImage($row->options->image,false,190) }}"
                                                         alt="{{ $row->name }}"
                                                    >
                                                </div>
                                            @endif

                                            <div class="product-title">
                                                <h4>
                                                    {{ $row->name }}
                                                    <span>
                                                        x{{ $row->qty }}
                                                    </span>
                                                </h4>
                                            </div>

                                            <div class="product-bottom">
                                                <div class="product-prices">
                                                    {{ priceFormat($row->price) }}
                                                </div>
                                            </div>

                                            <a href="{{ route('whishlist.addCart',['rowId' => $row->rowId]) }}"
                                               class="whish-to-cart"
                                            >
                                                {{ __("piclommerce::web.shop_add_basket") }}
                                            </a>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>{{ __("piclommerce::web.shop_no_whishlist") }}</p>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection