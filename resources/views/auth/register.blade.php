@extends("piclommerce::layouts.app")

@section('content')
    <div class="head-title">
        <div class="l-container">
            <h1>{{ __('piclommerce::web.user_create_account') }}</h1>
        </div>
    </div>
    <div class="l-container">
        <div class="login-register-section">
            @if(count($errors) > 0)
                @include("piclommerce::components.alert-error")
            @endif
            @include("piclommerce::components.users.register")
            <hr>
            <div class="links">
                <p>
                    {{ __('piclommerce::web.user_account_yet') }} <a href="{{ route('login') }}">{{ __('piclommerce::web.user_login_link') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
