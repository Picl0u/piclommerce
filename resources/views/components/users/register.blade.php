<form method="post" action="{{ route("register") }}">
    {{ csrf_field() }}

    <label>{{ __('piclommerce::web.user_civility') }}</label>
    <div class="form-item form-checkboxes">
        <label class="is-checkbox">
            <input type="radio" name="gender" value="M" checked="checked">
            {{ __('piclommerce::web.user_civility_mr') }}
        </label>
        <label class="is-checkbox">
            <input type="radio" name="gender" value="Mme">
            {{ __('piclommerce::web.user_civility_mrs') }}
        </label>
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_firstname') }}</label>
        <input type="text" name="firstname" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_lastname') }}</label>
        <input type="text" name="lastname" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_email') }}</label>
        <input type="email" name="email" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_password') }}</label>
        <input type="password" name="password" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_password_confirm') }}</label>
        <input type="password" name="password_confirmation" required="required" value="">
    </div>

    <div class="form-item">
        <label class="checkbox">
            <input type="checkbox" name="newsletter" checked="checked">
            {{ __('piclommerce::web.user_newsletter_subscribe') }}
        </label>
        <div class="desc">
            {{ __('piclommerce::web.user_newsletter_unsubscribe') }}
        </div>
    </div>

    <button type="submit">
        {{ __('piclommerce::web.user_register') }}
    </button>

</form>