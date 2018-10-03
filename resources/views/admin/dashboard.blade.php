@extends("piclommerce::layouts.admin")

@section("style")
    {!! Charts::styles() !!}
    {!! Charts::scripts() !!}
@endsection

@section("content")
<div class="page-title">
    <div class="is-row align-middle">
        <div class="is-col">
            <h1>
                <i class="fa fa-home"></i>
                {{ __('piclommerce::admin.navigation_dashboard') }}
                <span{{ __('piclommerce::admin.dashboard_welcome') }}</span>
            </h1>
        </div>
        <div class="is-col text-right">
            <nav class="breadcrumb">
                <span>{{ __('piclommerce::admin.navigation_dashboard') }}</span>
            </nav>
        </div>
    </div>
</div>

<div class="content-container">
    <div class="statistics">
        <div class="is-row">
            <div class="is-col is-30 is-hidden">

            </div>
            <div class="is-col is-70">
                <div class="widget">
                    <div class="widget-title">
                        {{ __('piclommerce::admin.dashboard_stats') }}
                        {{ __('piclommerce::admin.dashboard_for_the_year') }} {{ date("Y") }}
                    </div>
                    <div class="is-row is-col-stack-20">
                        <div class="is-col widget-value">
                            {{ __('piclommerce::admin.dashboard_sales') }}
                            <span>{{ priceFormat($pricesOrders) }}</span>
                        </div>
                        <div class="is-col widget-value">
                            {{ __('piclommerce::admin.dashboard_orders') }}
                            <span>{{ $totalOrders }}</span>
                        </div>
                        <div class="is-col widget-value">
                            {{ __('piclommerce::admin.dashboard_cart') }}
                            <span>
                                @if(!empty($totalOrders))
                                    {{ priceFormat($pricesOrders/$totalOrders) }}
                                @else
                                    {{ priceFormat(0) }}
                                @endif
                            </span>
                        </div>
                        <div class="is-col widget-value">
                            {{ __('piclommerce::admin.dashboard_users') }}
                            <span>{{ $custommers }}</span>
                        </div>
                    </div>

                    {!! $chart->html() !!}
                    {!! $chart->script() !!}
                </div>

                <div class="widget">
                    <div class="widget-title">{{ __('piclommerce::admin.dashboard_sales_and_products') }}</div>
                    <nav class="tabs" data-kube="tabs" data-equal="true">
                        <a href="#order-recent" class="is-active">
                            <i class="fa fa-shopping-cart"></i>
                            {{ __("piclommerce::admin.dashboard_recents_orders") }}
                        </a>
                        <a href="#product-views">
                            <i class="fa fa-eye"></i>
                            {{ __("piclommerce::admin.dashboard_most_view") }}
                        </a>
                        <a href="#product-sales">
                            <i class="fa fa-fire" aria-hidden="true"></i>
                            {{ __("piclommerce::admin.dashboard_most_sale") }}
                        </a>
                    </nav>
                    <section id="order-recent">
                        <div class="is-table-container">
                            <table class="is-bordered is-striped">
                                <thead>
                                <tr>
                                    <th>{{ __("piclommerce::admin.dashboard_order_reference") }}</th>
                                    <th>{{ __("piclommerce::admin.dashboard_order_user") }}</th>
                                    <th>{{ __("piclommerce::admin.dashboard_order_shipping") }}</th>
                                    <th>{{ __("piclommerce::admin.dashboard_order_total") }}</th>
                                    <th>{{ __("piclommerce::admin.dashboard_order_date") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($lastOrders as $order)
                                    <tr>
                                        <td>
                                            {{ $order->reference }}
                                        </td>
                                        <td>
                                            {{ $order->user_firstname." ".$order->user_lastname }}
                                        </td>
                                        <td>
                                            {{ $order->delivery_country_name }}
                                        </td>
                                        <td>
                                            {{ priceFormat($order->price_ttc) }} ({{$order->total_quantity}})
                                        </td>
                                        <td>
                                            {{ date('d/m/Y H:i',strtotime($order->created_at)) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section id="product-views">
                        <div class="is-table-container">
                            <table class="is-bordered is-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __("piclommerce::admin.dashboard_product_image") }}</th>
                                        <th>{{ __("piclommerce::admin.dashboard_product_name") }}</th>
                                        <th>{{ __("piclommerce::admin.dashboard_product_views") }}</th>
                                        <th>{{ __("piclommerce::admin.dashboard_product_percent") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productViews as $product)
                                        @if($product->numOfViews > 0)
                                        <tr>
                                            <td>
                                                @php $medias = $product->getMedias('image'); @endphp
                                                <img src="{{ resizeImage($medias['target_path'], 30 , 30) }}"
                                                     alt="{{ $medias['alt'] }}"
                                                     class="remodalImg" data-src="/{{ $medias['target_path'] }}"
                                                >
                                            </td>
                                            <td>
                                                <i>{{ $product->reference }}</i> - {{ $product->name }}
                                            </td>
                                            <td>
                                                {{ $product->numOfViews }}
                                            </td>
                                            <td>
                                                {{ round(($product->numOfViews/$totalProductViews)*100,0) }}%
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section id="product-sales">
                        <div class="is-table-container">
                            <table class="is-bordered is-striped">
                                <thead>
                                <tr>
                                    <th>{{ __("piclommerce::admin.dashboard_product_image") }}</th>
                                    <th>{{ __("piclommerce::admin.dashboard_product_name") }}</th>
                                    <th>{{ __("piclommerce::admin.dashboard_best_quantity") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bestSale as $product)
                                    <tr>
                                        <td>
                                            @php $medias = $product->getMedias('image'); @endphp
                                            <img src="{{ resizeImage($medias['target_path'], 30 , 30) }}"
                                                 alt="{{ $medias['alt'] }}"
                                                 class="remodalImg" data-src="/{{ $medias['target_path'] }}"
                                            >
                                        </td>
                                        <td>
                                            <i>{{ $product->ref }}</i> - {{ $product->name }}
                                        </td>
                                        <td>
                                            {{ $product->sum }}
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection