<div class="search-bar">
    <div class="l-container">
        <div class="is-container">
            @if(isset($sliders) && !empty($sliders) && !empty(setting('slider.dots')))
                <div class="slider-pagination">
                    @foreach($sliders as $key => $slide)
                        <span data-slide="{{ $key }}"></span>
                    @endforeach
                </div>
            @endif
            @if(isset($arianne) && !empty($arianne))
                <nav class="breadcrumb">
                    @foreach($arianne as $key => $link)
                        <a href="{{ $link}}">{{ $key }}</a>
                    @endforeach
                </nav>
            @endif
            <form class="is-push-right" method="get" action="{{ route("product.search") }}">
                {{ csrf_field() }}
                <input type="text"
                   name="keywords"
                   placeholder="{{ __('piclommerce::web.shop_product_search') }}"
                   value=""
                   required="required"
                >
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
                <div class="clear"></div>
            </form>
        </div>

    </div>
</div>