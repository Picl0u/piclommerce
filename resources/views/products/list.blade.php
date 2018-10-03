@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>{{ $category->name }} @if(isset($_GET['page'])) - Page {{ $_GET['page'] }}@endif</h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="l-container products-section">
        <div class="is-row">
            <div class="is-col is-25 sidebar">
                <h2>
                    @if(!empty($parent->name))
                        <a href="{{ route('product.list',['slug' => $parent->slug, 'id' => $parent->id]) }}">
                            {{ $parent->name }}
                        </a>
                    @else
                        <a href="{{ route('product.list',['slug' => $category->slug, 'id' => $category->id]) }}">
                            {{ $category->name }}
                        </a>
                    @endif
                </h2>
                <nav class="sidebar-navigation">
                    @if(!empty($parent->id))
                        {!!
                            nestableExtends($categories)
                            ->parent($parent->id)
                            ->route(['product.list' => 'slug'])
                            ->renderAsHtml()
                        !!}
                    @else
                        {!!
                           nestableExtends($categories)
                           ->parent($category->id)
                           ->route(['product.list' => 'slug'])
                           ->renderAsHtml()
                        !!}
                    @endif
                </nav>
            </div>
            <div class="is-col is-75 product-list">
                @if(!is_null($category->imageList) && !empty($category->imageList))
                    <div class="category-image">
                        @php $medias = $category->getMedias("imageList"); @endphp
                        @if($medias)
                            <img src="{{ $medias['target_path'] }}" alt="{{ $medias['alt'] }}"  class="hide-sm category-img">
                        @endif
                    </div>
                @elseif(!empty($parent->imageList))
                    <div class="category-image">
                        @php $medias = $parent->getMedias("imageList"); @endphp
                        @if($medias)
                            <img src="{{ $medias['target_path'] }}" alt="{{ $medias['alt'] }}"  class="hide-sm category-img">
                        @endif
                    </div>
                @endif
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
                    <form method="get"
                          action="{{ route('product.list',[
                          'slug' => $category->slug,
                          'id' => $category->id
                          ]) }}"
                    >
                        {{ csrf_field() }}
                        <label for="orderField">{{ __('piclommerce::web.shop_order_by') }}</label>
                        <select name="orderField" id="orderField">
                            <option value="pertinence" {!! ($order && $order == "pertinence")?'selected="selected"':'' !!}>
                                {{ __('piclommerce::web.shop_pertinence') }}
                            </option>
                            <option value="name_asc" {!! ($order && $order == "name_asc")?'selected="selected"':'' !!}>
                                {{ __('piclommerce::web.shop_nameAsc') }}
                            </option>
                            <option value="name_desc" {!! ($order && $order == "name_desc")?'selected="selected"':'' !!}>
                                {{ __('piclommerce::web.shop_nameDesc') }}
                            </option>
                            <option value="price_asc" {!! ($order && $order == "price_asc")?'selected="selected"':'' !!}>
                                {{ __('piclommerce::web.shop_priceAsc') }}
                            </option>
                            <option value="price_desc" {!! ($order && $order == "price_desc")?'selected="selected"':'' !!}>
                                {{ __('piclommerce::web.shop_priceDesc') }}
                            </option>
                        </select>
                    </form>
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
    </div>
@endsection