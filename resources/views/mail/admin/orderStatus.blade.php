@extends('piclommerce::layouts.mail')

@section('message')
    <table style="width:100%"  cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align:center; text-transform:uppercase;font-size:20px;">
                Mise à jours de commande
            </td>
        </tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td>
                {{ __("piclommerce::web.hello") }}  <strong>{{ $order->user_firstname }} {{ $order->user_lastname }}</strong><br>
                Cet email vous est envoyé car votre commande a été mise à jours.<br>
                Voici le statut de votre commande :
            </td>
        </tr>
        <tr><td style="height:20px;"></td></tr>
        <tr>
            <td style="font-size:20px;">
                {{ $status->name }}
            </td>
        </tr>

        <tr><td style="height:40px;"></td></tr>

        <tr><td style="height:1px; background-color:#CCC"></td></tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td style="text-transform:uppercase;font-size:20px;">
                {{ __("piclommerce::web.order") }} : {{ $order->reference }}
            </td>
        </tr>
        <tr>
            <td style="font-size:16px;color:#CCC;">
                {{ date('d/m/Y') }}
            </td>
        </tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td style="background-color:#eeeeee;padding:20px;">
                <table style="width:560px;margin:0 auto;" cellspacing="0" cellpadding="0">
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product['name'] }}</strong> - Réf : {{ $product['ref'] }}
                                @isset($product['declinaisons'])
                                    <br> {{ $product['declinaisons']}}
                                @endisset
                            </td>
                            <td>
                                x{{ $product['quantity'] }}
                            </td>
                            <td>
                                {{ priceFormat($product['price_ttc']) }}
                            </td>
                        </tr>
                        <tr><td colspan="3" style="height:20px;"></tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align:right; padding:0 20px;">
                            {{ __('piclommerce::web.cart_sub_total') }}
                        </td>
                        <td>{{ $order->price_ht }} &euro;</td>
                    </tr>
                    <tr><td colspan="3" style="height:20px;"></tr>

                    <tr>
                        <td colspan="2" style="text-align:right; padding:0 20px;">
                            {{ __('piclommerce::web.cart_vat') }}({{ $order->vat_percent }}%)
                        </td>
                        <td>{{ $order->vat_price }} &euro;</td>
                    </tr>

                    <tr><td colspan="3" style="height:20px;"></tr>

                    @if(!is_null($order->coupon_price) && !empty($order->coupon_price))
                        <tr>
                            <td colspan="2" style="text-align:right; padding:0 20px;">
                                <strong>{{ __("piclommerce::web.cart_coupon_reduction") }} ({{ $order->coupon_name }})</strong>
                            </td>
                            <td>-{{ priceFormat($order->coupon_price) }}</td>
                        </tr>
                        <tr><td colspan="3" style="height:20px;"></tr>
                    @endif

                    <tr>
                        <td colspan="2" style="text-align:right; padding:0 20px;">
                            {{ __('piclommerce::web.cart_shipping') }}
                        </td>
                        <td>{{ priceFormat($order->shipping_price) }}</td>
                    </tr>

                    <tr><td colspan="3" style="height:20px;"></tr>

                    <tr>
                        <td colspan="2" style="text-align:right; padding:0 20px;">
                            {{ __('piclommerce::web.cart_total') }}
                        </td>
                        <td>{{ priceFormat($order->price_ttc) }}</td>
                    </tr>

                </table>
            </td>
        </tr>

        <tr><td style="height:40px;"></td></tr>

        <tr>
            <td>

                <table style="width:100%"  cellspacing="0" cellpadding="0">
                    <tr>

                        <td style="width:50%;">
                            <span style="text-transform:uppercase;font-size:16px;">
                                {{ __('piclommerce::web.user_delivery_address') }}
                            </span><br>
                            <strong>
                                {{ $order->delivery_gender }} {{ $order->delivery_firstname }} {{ $order->delivery_lastname }}
                            </strong><br>
                            {{ $order->delivery_address }} {{ $order->delivery_additional_address }}<br>
                            {{ $order->delivery_zip_code }} {{ $order->delivery_city }}<br>
                            {{ $order->delivery_country_name }}<br>
                            {{ __('piclommerce::web.user_phone') }} : {{ $order->delivery_phone }}
                        </td>

                        <td style="width:50%;">
                            <span style="text-transform:uppercase;font-size:16px;">
                                {{ __('piclommerce::web.user_billing_address') }}
                            </span><br>
                            <strong>
                                {{ $order->billing_gender }} {{ $order->billing_firstname }} {{ $order->billing_lastname }}
                            </strong><br>
                            {{ $order->billing_address }} {{ $order->billing_additional_address }}<br>
                            {{ $order->billing_zip_code }} {{ $order->billing_city }}<br>
                            {{ $order->billing_country_name }}<br>
                            {{ __('piclommerce::web.user_phone') }} : {{ $order->billing_phone }}
                        </td>

                    </tr>
                </table>
            </td>
        </tr>

    </table>
@endsection