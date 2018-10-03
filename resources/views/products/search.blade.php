@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.shop_product_search') }} : {{ $keywords }} @if(isset($_GET['page'])) - Page {{ $_GET['page'] }}@endif
            </h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="l-container products-section">
        <div class="product-list">

            <div class="filters-products">
                <div class="total-product">
                    {{ __('piclommerce::web.shop_there_is') }}
                    {{ $products->total() }}
                    @if($products->total() > 1)
                        {{ __('piclommerce::web.shop_products') }}
                    @else
                        {{ __('piclommerce::web.shop_product') }}
                    @endif
                </div>
            </div>
            <div class="is-row is-bricks">
                @foreach($products as $product)
                    <div class="is-col is-25 is-col-stack-24">
                        @include("piclommerce::components.product", compact("product"))
                    </div>
                @endforeach
            </div>

            {{ $products->links() }}

        </div>
    </div>
@endsection