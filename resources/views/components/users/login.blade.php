<form method="post" action="{{ route("login") }}">
    {{ csrf_field() }}
    <div class="form-item">
        <label>{{ __('piclommerce::web.user_email') }}</label>
        <input type="email" name="email" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_password') }}</label>
        <input type="password" name="password" required="required" value="">
    </div>

    <div class="form-item">
        <label class="checkbox">
            <input type="checkbox" name="remember" checked="checked"> {{ __('piclommerce::web.user_remember') }}
        </label>
    </div>

    <button type="submit">
        {{ __('piclommerce::web.user_login') }}
    </button>
    <div class="forgot-password">
        <a href="{{ route('password.request') }}">
            {{ __("piclommerce::web.user_password_forget") }}
        </a>
    </div>

</form>