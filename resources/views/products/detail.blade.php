@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>{{ $product->shopCategory->name }}</h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="product-detail">
        <div class="l-container">
            <div class="page-actions">
                <a href="{{ URL::previous() }}" class="return">
                    <i class="fa fa-arrow-left"></i>
                    {{ __('piclommerce::web.shop_return_list') }}
                </a>
            </div>
            <div class="product">
                <div class="is-row">
                    <div class="is-col is-40">
                        <div class="images">
                            @php $images = $product->getMedias("imageList"); @endphp
                            @if($images)
                            @foreach($images as $key => $image)
                                @if(!empty(setting('products.zoomEnable')))
                                    <div class="zoom" data-url="/{{ $image['target_path']}}" data-img="{{ $key }}" data-uuid="{{ $image['uuid'] }}">
                                @else
                                    <div class="product-show" data-img="{{ $key }}" data-uuid="{{ $image['uuid'] }}">
                                @endif
                                @if(!empty(setting('products.modalEnable')))
                                    <a href="#product-image-modal" class="product-image-modal">
                                @endif
                                    <img src="/{{ $image['target_path']}}" alt="{{ $image['alt']}}" >
                                @if(!empty(setting('products.modalEnable')))
                                    </a>
                                @endif
                                </div>
                             @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="is-col is-10">

                        <div class="products-col">
                            <div class="socials">
                                <a href="{{ route('whishlist.product.add') }}"
                                   data-product="{{ $product->uuid }}"
                                   class="add-to-whishlist"
                                >
                                    <i class="fa fa-heart"></i>
                                </a>
                                <a href="mailto:?subject={{ $product->name }} - {{ setting("generals.websiteName") }}&amp;body={{ $product->name}} : {{ url()->current() }}">
                                    <i class="fa fa-envelope"></i>
                                </a>
                            </div>
                            <div class="vignettes">
                                @if($images)
                                    @foreach($images as $key => $image)
                                        <img
                                            src="{{ resizeImage($image['target_path'], 63, 63) }}"
                                            alt="{{ $image['alt'] }}"
                                            data-img="{{ $key }}"
                                            data-uuid="{{ $image['uuid'] }}"
                                        >
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col is-50">
                        <div class="product-infos">
                            <div class="infos">
                                <h2>{{ $product->name}}</h2>
                                <div class="summary">
                                    {!! $product->summary!!}
                                </div>
                                <div class="product-prices">
                                    @if(!empty($product->reduce_price) || !empty($product->reduce_percent))
                                        @if(is_null($product->reduce_date_begin) || $product->reduce_date_begin == '0000-00-00 00:00:00')
                                            <span>{{ priceFormat($product->price_ttc) }}</span>
                                            @if(!empty($product->reduce_price))
                                                {{ priceFormat($product->price_ttc - $product->reduce_price) }}
                                            @endif
                                            @if(!empty($product->reduce_percent))
                                                {{ priceFormat(
                                                    $product->price_ttc -
                                                    ($product->price_ttc * (($product->reduce_percent/100)))
                                                ) }}
                                            @endif
                                        @elseif($product->reduce_date_begin <= date('Y-m-d H:i:s') && $product->reduce_date_end > date('Y-m-d H:i:s'))
                                            <span>{{ priceFormat($product->price_ttc) }}</span>
                                            @if(!empty($product->reduce_price))
                                                {{ priceFormat($product->price_ttc - $product->reduce_price) }}
                                            @endif
                                            @if(!empty($product->reduce_percent))
                                                {{ priceFormat(
                                                    $product->price_ttc -
                                                    ($product->price_ttc * (($product->reduce_percent/100)))
                                                ) }}
                                            @endif
                                        @else
                                            {{ priceFormat($product->price_ttc) }}
                                        @endif
                                    @else
                                        {{ priceFormat($product->price_ttc) }}
                                    @endif

                                </div>
                                @if($product->stock_available > 0)
                                    <form method="post"
                                          action="{{ route('cart.product.add') }}"
                                          class="form-product-to-basktet"
                                          data-attributes="{{ route('cart.product.attributes') }}"
                                    >
                                        {{ csrf_field() }}
                                        <input type="hidden" name="product_id" value="{{ $product->uuid }}">

                                        @foreach($declinaisons as $key => $attributes)
                                            <div class="form-item">
                                                <label>{{ $key }}</label>
                                                <select name="declinaisons[{{ $key }}]">
                                                    <option value="0">Selectionner</option>
                                                    @foreach($attributes as $attribute)
                                                        <option value="{{ $attribute }}">{{ $attribute }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach

                                        <div class="form-item">
                                            <label for="quantity">{{ __("piclommerce::web.shop_quantity") }}</label>
                                            <input type="number" name="quantity" min="1" max="{{ $product->stock_available }}" value="1">
                                        </div>

                                        <button type="submit">
                                            <i class="fa fa-shopping-cart"></i>
                                            {{ __("piclommerce::web.shop_add_basket") }}
                                        </button>
                                    </form>
                                @else
                                    <div class="no-product">
                                        {{ __("piclommerce::web.shop_product_unavailable") }}
                                    </div>
                                @endif
                                @if(!empty(setting('products.socialEnable')))
                                    {!! $share !!}
                                @endif
                            </div>
                        </div>
                    </div>
                    </div>

                    <nav class="tabs" data-kube="tabs">
                        <a href="#description" class="is-active">{{ __("piclommerce::web.shop_description") }}</a>
                        @if(setting('products.commentEnable'))
                            <a href="#comments">{{ __("piclommerce::web.shop_comments") }} ({{ count($comments) }})</a>
                        @endif
                    </nav>

                    <section id="description" class="tab-content">
                        {!! $product->description !!}
                    </section>
                    @if(setting('products.commentEnable'))
                        <section id="comments" class="tab-content">
                            <div class="comments">
                                @foreach($comments as $comment)
                                    <div class="comment">
                                        <div class="user-date">
                                            <div class="user">
                                                {{ $comment->firstname }} {{ $comment->lastname }}
                                            </div>
                                            <div class="date">
                                                {{ $comment->created_at->format('d/m/Y à H:i') }}
                                            </div>
                                        </div>
                                        <p>
                                            {{ $comment->comment }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            @if(auth()->check())
                                <form method="post"
                                      action="{{ route('product.comment',['uuid' => $product->uuid]) }}"
                                >
                                    {{ csrf_field() }}
                                    @if(count($errors) > 0)
                                        <div class="message error" data-component="message">
                                            <h5 style="color:#fff">Attention !</h5>
                                            <ul>
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <span class="close small"></span>
                                        </div>
                                    @endif
                                    <div class="form-item">
                                        <label for="form-comment">
                                            {{ __("piclommerce::web.shop_comment") }}
                                        </label>
                                        <textarea rows="0" cols="0" name="comments" id="form-comment" required="required"></textarea>
                                    </div>
                                    <button type="submit">
                                        {{ __("piclommerce::web.send") }}
                                    </button>
                                </form>
                            @else
                                <div class="connect-user">
                                    <p>
                                        <i class="fa fa-exclamation-triangle"></i>
                                        {{ __("piclommerce::web.shop_comment_desc") }}
                                    </p>
                                </div>
                            @endif
                        </section>
                    @endif

                </div>
            </div>
        </div>

        <div class="l-container">
            <div class="related-products">
                <div class="title">
                    <div>
                        <h4>Vous aimerez aussi...</h4>
                    </div>
                    @if(count($relatedProducts) > 6)
                        <div class="week-arrows">
                    <span class="week-prev">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                            <span class="week-next">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                        </div>
                    @endif
                </div>
                <div class="slider-related-products">
                    @if(isset($relatedProducts) && !empty($relatedProducts))
                        @foreach($relatedProducts as $product)
                           @include("piclommerce::components.product", compact("product"))
                        @endforeach
                    @else
                        @foreach($productAssociates as $productAssociate)
                            @php $product = $productAssociate->Product @endphp
                            @include("piclommerce::components.product", compact("product"))
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="remodal remodal-product" data-remodal-id="product-image-modal"
         data-remodal-options="hashTracking: false">
        <div data-remodal-action="close" class="remodal-close"></div>
        <div class="product-img center-text text-center"></div>
    </div>
@endsection