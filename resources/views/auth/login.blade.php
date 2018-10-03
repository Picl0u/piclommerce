@extends("piclommerce::layouts.app")

@section('content')
    <div class="head-title">
        <div class="l-container">
            <h1>{{ __('piclommerce::web.user_login') }}</h1>
        </div>
    </div>
<div class="l-container">
    <div class="login-register-section">
        @if(count($errors) > 0)
            @include("piclommerce::components.alert-error")
        @endif
        @include("piclommerce::components.users.login")
        <hr>
        <div class="links">
            <p>
                {{ __('piclommerce::web.user_no_account_yet') }} <a href="{{ route('register') }}">{{ __('piclommerce::web.user_register_link') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
