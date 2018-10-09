@extends("piclommerce::layouts.app")

@section("content")
    @if(count($sliders) > 0)
        <div
            class="slider {{ setting('slider.type') }}"
            data-transition="{{ setting('slider.transition') }}"
            data-slideDuration="{{ setting('slider.slideDuration') }}"
            data-transitionDuration="{{ setting('slider.transitionDuration') }}"
            data-arrows="{{ (!empty(setting('slider.arrows')))?'true':'false' }}"
        >
            @foreach($sliders as $slide)
                @php $medias = $slide->getMedias("image"); @endphp
                <div class="slide">
                    @if($medias)
                    <img
                        src="/{{ $medias['target_path'] }}"
                        alt="{{ $medias['alt'] }}"
                        class="img-to-background"
                    >
                    @endif
                    <div class="slide-description {{ $slide->position }}">
                        <div class="content">
                            {!! $slide->description !!}
                        </div>
                    </div>
                    @if(!empty($slide->link))
                        <a href="{{ $slide->link }}"></a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @include('piclommerce::components.search-bar', compact('sliders'))

    @if(count($banners) > 0)
        <div class="banners">
            <div class="l-container">
                <div class="is-row is-col-stack-20">
                    @foreach($banners as $banner)
                        @php $medias = $banner->getMedias("image"); @endphp
                        @if($medias)
                            <div class="is-col banner">
                                {!! ($banner->link)?'<a href="'.$banner->link.'">':'' !!}
                                <img src="{{ $medias['target_path'] }}" alt="{{ $medias['alt'] }}">
                                {!! ($banner->link)?'</a>':'' !!}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(count($categories) > 0)
        <div class="l-container">
            <div class="is-row is-col-stack-20 categories">
                @foreach($categories as $category)
                    <div class="is-col is-33 category">
                        @php $medias = $category->getMedias("image"); @endphp
                        @if($medias)
                            <img src="{{ $medias['target_path'] }}"
                                 alt="{{ $medias['alt'] }}"
                                 {!! ($medias['description'])?'title="'.$medias['description'].'"':'' !!}
                                 class="img-to-background"
                            >
                        @endif
                        <div class="mask"></div>
                        <div class="content">
                            <h2>{{ $category->name }}</h2>
                            <div class="arrow">
                                <i class="fa fa-arrow-right"></i>
                            </div>
                        </div>
                        <a href="{{ route('product.list',[
                            'slug' => $category->translate('slug'),
                            'id' => $category->id
                            ]) }}"></a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(count($bestSale) > 0 || count($flashSales) > 0)
        <div class="l-container">
            <div class="is-row is-col-stack-20 flash-bests-sales is-items-middle">

                @if(count($bestSale) > 0)
                    <div class="is-col best-sales">
                        <div class="title">
                            <h3>{{ __('piclommerce::web.shop_best_sale') }}</h3>
                            @if(count($bestSale) > 1)
                                <div class="flash-arrows">
                                    <span class="best-prev">
                                        <i class="fa fa-arrow-left"></i>
                                    </span>
                                    <span class="best-next">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="best-sale-slider">
                            @foreach($bestSale as $product)
                                <div class="product">
                                    @php $productLink = route('product.show',['slug' => $product->slug,'id' => $product->id]);  @endphp

                                    @if(!empty($product->reduce_price) || !empty($product->reduce_percent))
                                        @if(is_null($product->reduce_date_begin) || $product->reduce_date_begin == '0000-00-00 00:00:00')
                                            <div class="percent">
                                                {{ percentReduc($product->price_ttc, $product->reduce_price, $product->reduce_percent) }}
                                            </div>
                                        @elseif($product->reduce_date_begin <= date('Y-m-d H:i:s') && $product->reduce_date_end > date('Y-m-d H:i:s'))
                                            <div class="percent">
                                                {{ percentReduc($product->price_ttc, $product->reduce_price, $product->reduce_percent) }}
                                            </div>
                                        @endif
                                    @endif

                                    <div class="product-image">
                                        @if($product->image)
                                            @php $medias = $product->getMedias("image"); @endphp
                                            <a href="{{ $productLink }}">
                                                <img
                                                    src="{{ resizeImage($medias['target_path'], null, 190) }}"
                                                    alt="{{ $medias['alt'] }}"
                                                    {!! ($medias['description'])?'title="'.$medias['description'].'"':'' !!}
                                                >
                                            </a>
                                        @endif
                                    </div>
                                    <div class="product-title">
                                        <h4>
                                            <a href="{{ $productLink }}">
                                                {{ $product->name }}
                                            </a>
                                        </h4>
                                    </div>

                                    <div class="product-bottom">
                                        <div class="product-prices">
                                            @if(!empty($product->reduce_price) || !empty($product->reduce_percent))
                                                @if(is_null($product->reduce_date_begin) || $product->reduce_date_begin == '0000-00-00 00:00:00')
                                                    @if(!empty($product->reduce_price))
                                                        {{ priceFormat($product->price_ttc - $product->reduce_price) }}
                                                    @endif
                                                    @if(!empty($product->reduce_percent))
                                                        {{ priceFormat(
                                                            $product->price_ttc -
                                                            ($product->price_ttc * (($product->reduce_percent/100)))
                                                        ) }}
                                                    @endif
                                                    <span>{{ priceFormat($product->price_ttc) }}</span>
                                                @elseif($product->reduce_date_begin <= date('Y-m-d H:i:s') && $product->reduce_date_end > date('Y-m-d H:i:s'))
                                                    @if(!empty($product->reduce_price))
                                                        {{ priceFormat($product->price_ttc - $product->reduce_price) }}
                                                    @endif
                                                    @if(!empty($product->reduce_percent))
                                                        {{ priceFormat(
                                                            $product->price_ttc -
                                                            ($product->price_ttc * (($product->reduce_percent/100)))
                                                        ) }}
                                                    @endif
                                                    <span>{{ priceFormat($product->price_ttc) }}</span>
                                                @else
                                                    {{ priceFormat($product->price_ttc) }}
                                                @endif
                                            @else
                                                {{ priceFormat($product->price_ttc) }}
                                            @endif
                                        </div>
                                        <div class="product-actions">
                                            <a href="{{ $productLink }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('whishlist.product.add') }}"
                                               data-product="{{ $product->uuid }}"
                                               class="add-to-whishlist"
                                            >
                                                <i class="fa fa-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($flashSales) > 0)
                    <div class="is-col flash-sales">
                        <div class="title">
                            <h3>{{ __('piclommerce::web.shop_flash_sales_title') }}</h3>
                            @if(count($flashSales) > 1)
                                <div class="flash-arrows">
                                    <span class="best-prev">
                                        <i class="fa fa-arrow-left"></i>
                                    </span>
                                        <span class="best-next">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flash-sales-slider">
                            @include("piclommerce::components.flash-sale", compact("flashSales"))
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(count($weekSelections) > 0)
        <div class="l-container">
            <div class="week-selections">
                <div class="title">
                    <div>
                        <h4>{{ __("piclommerce::web.shop_selection") }}</h4>
                    </div>
                    @if(count($weekSelections) > 6)
                        <div class="week-arrows">
                            <span class="week-prev">
                                <i class="fa fa-arrow-left"></i>
                            </span>
                            <span class="week-next">
                                <i class="fa fa-arrow-right"></i>
                            </span>
                        </div>
                    @endif
                </div>
                <div class="slider-week-sections">
                    @foreach($weekSelections as $product)
                        @include("piclommerce::components.product", compact("product"))
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(count($contents) > 0)
        <div class="home-contents">
            <div class="l-container">
                <div class="is-row is-col-stack-20">
                    @foreach($contents as $content)
                        <div class="is-col">
                            @if($content->image)
                                <div class="content-image">
                                    @php $medias = $content->getMedias("image"); @endphp
                                    @if($medias)
                                        <img src="{{ $medias['target_path'] }}"
                                             alt="{{ $medias['alt'] }}"
                                             {!! ($medias['description'])?'title="'.$medias['description'].'"':'' !!}
                                        >
                                    @endif
                                </div>
                            @endif
                            <h5>{{ $content->name }}</h5>
                            {!! $content->summary !!}
                            <a href="{{ route('content.index',[ 'slug' => $content->slug, 'id' => $content->id ]) }}" class="read-more">
                                {{ __('piclommerce::web.read_more') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection