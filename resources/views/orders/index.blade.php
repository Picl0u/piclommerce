@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.user_my_orders') }}
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
                        <h2>{{ __('piclommerce::web.user_my_orders') }}</h2>
                    </div>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif

                    <div class="orders">
                        <div class="is-table-container">
                            <table class="is-bordered is-striped" width="100%">
                                <thead>
                                <tr>
                                    <th>{{ __('piclommerce::web.order_ref') }}</th>
                                    <th>{{ __('piclommerce::web.order_delivery') }}</th>
                                    <th>{{ __('piclommerce::web.order_product_number') }}</th>
                                    <th>{{ __('piclommerce::web.order_total') }}</th>
                                    <th>{{ __('piclommerce::web.order_state') }}</th>
                                    <th>{{ __('piclommerce::web.order_date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                        {{ $order->reference }}
                                        <td>
                                            {{ $order->delivery_address }} {{ $order->delivery_additionnal_address }}<br>
                                            {{ $order->delivery_zip_code }} {{ $order->delivery_city }}<br>
                                            {{ $order->delivery_country_name }}
                                        </td>
                                        <td>
                                            {{ $order->total_quantity }}
                                        </td>
                                        <td>
                                            {{ priceFormat($order->price_ttc) }}
                                        </td>
                                        <td>
                                                <span class="label"
                                                <?=
                                                    (!empty($order->Status->color))?
                                                        'style="background-color:'.$order->Status->color.'; color:#FFF;"'
                                                        :'';
                                                    ?>
                                                >
                                                    {{ $order->Status->name }}
                                                </span>
                                        </td>
                                        <td>
                                            {{ $order->created_at->format('d/m/Y à H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('order.invoice',['uuid' => $order->uuid]) }}">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                {{ __('piclommerce::web.order_invoice') }}
                                            </a>
                                            <a href="{{ route('order.show',['uuid' => $order->uuid]) }}">
                                                <i class="fa fa-eye"></i>
                                                {{ __('piclommerce::web.order_view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
@endsection