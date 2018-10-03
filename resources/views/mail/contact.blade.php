@extends('piclommerce::layouts.mail')

@section('message')
    <table style="width:100%"  cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align:center; text-transform:uppercase;font-size:20px;">
                {{ __("piclommerce::web.contact_title") }}
            </td>
        </tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td>
                {{ __("piclommerce::web.hello") }},<br>
                {!! __("piclommerce::web.contact_mail_message") !!}

            </td>
        </tr>
        <tr><td style="height:20px;"></td></tr>
        <tr><td style="height:1px; background-color:#CCC"></td></tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td style="">
                <strong>{{ __("piclommerce::web.user_lastname") }} : </strong> {{ $contact['lastname'] }}<br>
                <strong>{{ __("piclommerce::web.user_firstname") }} : </strong> {{ $contact['firstname'] }}<br>
                <strong>{{ __("piclommerce::web.user_email") }} : </strong> <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a><br>
                <strong>{{ __("piclommerce::web.contact_message") }} : </strong><br> {!! nl2br($contact['message']) !!}
            </td>
        </tr>
        <tr><td style="height:40px;"></td></tr>
    </table>
@endsection