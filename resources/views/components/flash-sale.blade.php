@foreach($flashSales as $product)
    <div class="product">
        @php
            $productLink = route('product.show',[
                'slug' => $product->slug,
                'id' => $product->id
            ]);
        @endphp
        <div class="is-row">
            <div class="is-col is-50 product-image">
                @if($product->image)
                    @php $medias = $product->getMedias("image"); @endphp
                    <a href="{{ $productLink }}">
                        <img
                            src="{{ resizeImage($medias['target_path'], null, 280) }}"
                            alt="{{ $medias['alt'] }}"
                            {!! ($medias['description'])?'title="'.$medias['description'].'"':'' !!}
                        >
                    </a>
                @endif
            </div>
            <div class="is-col is-50 product-infos">
                <div class="countdown" data-date="{{ formatDate($product->reduce_date_end,'Y-m-d H:i:s')}}">
                    <div class="count days">
                        <p>{{ trans('piclommerce::web.days') }}</p>
                        <span>8</span>
                    </div>
                    <div class="count hours">
                        <p>{{ trans('piclommerce::web.hours') }}</p>
                        <span>13</span>
                    </div>
                    <div class="count minutes">
                        <p>{{ trans('piclommerce::web.minutes') }}</p>
                        <span>0</span>
                    </div>
                    <div class="count seconds">
                        <p>{{ trans('piclommerce::web.seconds') }}</p>
                        <span>0</span>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="product-categories">
                    <a href="{{ route("product.list", ['slug' => $product->translate('category_slug'), 'id' => $product->category_id]) }}">
                        {{ $product->category_name }}
                    </a>
                </div>
                <h3>
                    <a href="{{ $productLink }}">
                        {{ $product->name }}
                    </a>
                </h3>
                <div class="product-summary">
                    {!! $product->summary !!}
                </div>
                <div class="product-prices">
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
                </div>
                <div class="product-stock">
                    {{ trans('piclommerce::web.shop_still_available') }} : {{ $product->stock_available }}
                </div>
                <div class="product-action">
                    <a href="{{ $productLink }}">
                        <i class="fa fa-long-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
@endforeach