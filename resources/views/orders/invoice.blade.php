<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $order->reference }}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        h1,h2,h3,h4,p,span,div { font-family: DejaVu Sans; }
    </style>
</head>
<body>

<div style="clear:both; position:relative;">

    <div style="position:absolute; left:0pt; width:250pt;">
        <img class="img-rounded"
             style="max-height:{{ config('ikCommerce.invoiceLogoHeight') }}"
             src="{{ asset(str_replace('\\', '/',setting('generals.LogoInvoice'))) }}"
        >
    </div>

    <div style="margin-left:300pt;">
        <strong>Date: </strong> {{ $date }}<br>
        <strong>{{ __('piclommerce::web.cart_invoice') }} : </strong> {{ $order->reference }}
        <br />
    </div>

</div>
<br>
<h2>{{ __('piclommerce::web.cart_invoice') }} : {{ $order->reference }}</h2>

<div style="clear:both; position:relative;">
    <div style="position:absolute; width:250pt;">
        <h4>{{ __('piclommerce::web.cart_trader') }}</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                {{ $merchant['name'] }}<br>
                {{ __('piclommerce::web.cart_siret') }} : {{ $merchant['siret'] }}<br>
                @if(!empty($merchant['phone']) && !is_null($merchant['phone']))
                    {{ __('piclommerce::web.user_phone_min') }} : {{ $merchant['phone'] }}<br>
                @endif
                @if(!empty($merchant['tva']) && !is_null($merchant['tva']))
                    {{ __('piclommerce::web.cart_vat_number') }} : {{ $merchant['tva'] }}<br>
                @endif
                @if(!empty($merchant['rcs']) && !is_null($merchant['rcs']))
                    {{ $merchant['rcs'] }}<br>
                @endif
                {{ $merchant['address'] }}<br>
                {{ $merchant['zip'] }} {{ $merchant['city'] }}<br>
                {{ $merchant['country'] }}
            </div>
        </div>
    </div>
    <div style="margin-left:300pt;">
        <h4>{{ __('piclommerce::web.cart_custommer') }}</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                {{ $custommer['name'] }}<br>
                {{ __('piclommerce::web.user_phone_min') }} : {{ $custommer['phone'] }}<br>
                {{ $custommer['address'] }}<br>
                {{ $custommer['zip'] }} {{ $merchant['city'] }}<br>
                {{ $custommer['country'] }}
            </div>
        </div>
    </div>
</div>
<br>
<br>
<br>
<br>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>{{ __('piclommerce::web.cart_reference') }}</th>
        <th>{{ ucfirst(__('piclommerce::web.cart_product')) }}</th>
        <th>{{ __('piclommerce::web.cart_quantity') }}</th>
        <th>{{ __('piclommerce::web.cart_unit_price') }}</th>
        <th>{{ __('piclommerce::web.cart_sub_total') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->ref }}</td>
            <td>
                {{ $item->name }}
                @isset($item->declinaisons)
                    <br> {{ $item->declinaisons }}
                @endisset
            </td>
            <td>{{ $item->quantity }}</td>
            <td>{{ priceFormat($item->price_ht) }}</td>
            <td>{{ priceFormat($item->price_ttc) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div style="clear:both; position:relative;">
    @if($notes)
        <div style="position:absolute; left:0pt; width:250pt;">
            <h4>{{ __('piclommerce::web.cart_notes') }}<:</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! $notes !!}
                </div>
            </div>
        </div>
    @endif
    <div style="margin-left: 300pt;">
        <h4>Total:</h4>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td><strong> {{ __('piclommerce::web.cart_sub_total') }}</strong></td>
                <td>{{priceFormat( $order->price_ht) }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('piclommerce::web.cart_vat') }}({{ $order->vat_percent }}%)</strong></td>
                <td>{{ priceFormat($order->vat_price) }}</td>
            </tr>
            @if(!is_null($order->coupon_price) && !empty($order->coupon_price))

                <tr>
                    <td><strong>{{ __("piclommerce::web.cart_coupon_reduction") }} ({{ $order->coupon_name }})</strong></td>
                    <td>-{{ priceFormat($order->coupon_price) }}</td>
                </tr>
            @endif
            <tr>
                <td><strong>{{ __('piclommerce::web.cart_shipping') }}</strong></td>
                <td>{{ priceFormat($order->shipping_price) }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('piclommerce::web.cart_total') }}</strong></td>
                <td><strong>{{ priceFormat($order->price_ttc) }}</strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@if (!empty(setting('generals.invoiceFooter')) && !is_null(setting('generals.invoiceFooter')))
    <br />
    <div class="well">
        {{ setting('generals.invoiceFooter') }}
    </div>
@endif

</body>
</html>