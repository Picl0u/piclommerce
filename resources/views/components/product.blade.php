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

    <div class="product-categories">
        <a href="{{ route("product.list", ['slug' => $product->translate('category_slug'), 'id' => $product->category_id]) }}">
            {{ $product->translate('category_name') }}
        </a>
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