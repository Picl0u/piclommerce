@extends('piclommerce::layouts.mail')

@section('message')
    <table style="width:100%"  cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align:center; text-transform:uppercase;font-size:20px;">
                {{ __("piclommerce::web.shop_comment_object") }} {{ $product->ref }} - {{ $product->name }}
            </td>
        </tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td>
                {{ __("piclommerce::web.hello") }}, <br>
                {{ __("piclommerce::web.shop_comment_mail_text") }}
                <a href="{{ route('product.show',['slug' => $product->slug, 'id' => $product->id]) }}">
                    {{ $product->name }}
                </a>.<br>
                {{ __("piclommerce::web.shop_comment_mail_detail") }}
            </td>
        </tr>
        <tr><td style="height:20px;"></td></tr>
        <tr>
            <td style="font-size:12px;">
                <ul>
                    <li>
                        <strong>{{ __("piclommerce::web.shop_product") }} : </strong> {{ $product->name }}
                    </li>
                    <li>
                        <strong>{{ __("piclommerce::web.shop_product_link") }}  : </strong>
                        <a href="{{ route('product.show',['slug' => $product->slug, 'id' => $product->id]) }}">
                            {{ __("piclommerce::web.click") }}
                        </a>
                    </li>
                    <li>
                        <strong>{{ __("piclommerce::web.user") }} : </strong> {{ $user->firstname }} {{ $user->lastname }}
                    </li>
                    <li>
                        <strong>{{ __("piclommerce::web.user_email") }} : </strong>
                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                    </li>
                    <li>
                        <strong>{{ __("piclommerce::web.shop_comment") }} : </strong><br>
                        {!! nl2br($comment) !!}
                    </li>
                </ul>
            </td>
        </tr>

        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td>
                {{ __("piclommerce::web.shop_comment_mail_bo") }}
            </td>
        </tr>
        <tr><td style="height:20px;"></td></tr>

    </table>
@endsection