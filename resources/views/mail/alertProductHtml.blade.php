@extends('piclommerce::layouts.mail')

@section('message')
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="font-size:18px;line-height:26px;">
                Bonjour, une alerte de stock vient d'être lancée.
            </td>
        </tr>
        <tr><td style="height:10px;"></td></tr>
        <tr>
            <td style="font-size:18px;line-height:26px;">
                Voici le récaptulatif de l'alerte
            </td>
        </tr>
        <tr><td style="height:20px;"></td></tr>
    </table>

    <table>
        @foreach($products as $product)
            <tr>
                <td>
                    <strong>Nom du produit :</strong> {{ $product['name'] }}<br>
                    <strong>Référence du produit :</strong> {{ $product['ref'] }}<br>
                    <strong>Quantité restante :</strong> {{ $product['quantity'] }}<br>
                    @isset($product['declinaisons'])
                        <strong>Déclinaison :</strong>{{ $product['declinaisons']}}<br>
                    @endisset
                </td>
            </tr>
        @endforeach
    </table>
@endsection
