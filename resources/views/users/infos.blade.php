@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.user_my_account') }}
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
                        <h2>{{ __('piclommerce::web.user_update_infos') }}</h2>
                    </div>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif
                    <form method="post" action="{{ route("user.infos.update") }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <label>{{ __('piclommerce::web.user_civility') }}</label>
                        <div class="form-item form-checkboxes">
                            <label class="is-checkbox">
                                <input type="radio" name="gender" value="M" {!! ($user->gender == "M")?'checked="checked"':'' !!}>
                                {{ __('piclommerce::web.user_civility_mr') }}
                            </label>
                            <label class="is-checkbox">
                                <input type="radio" name="gender" value="Mme" {!! ($user->gender == "Mme")?'checked="checked"':'' !!}>
                                {{ __('piclommerce::web.user_civility_mrs') }}
                            </label>
                        </div>

                        <div class="form-item">
                            <label>{{ __('piclommerce::web.user_firstname') }}</label>
                            <input type="text" name="firstname" required="required" value="{{ $user->firstname }}">
                        </div>

                        <div class="form-item">
                            <label>{{ __('piclommerce::web.user_lastname') }}</label>
                            <input type="text" name="lastname" required="required" value="{{ $user->lastname }}">
                        </div>

                        <div class="form-item">
                            <label>{{ __('piclommerce::web.user_email') }}</label>
                            <input type="email" name="email" required="required" value="{{ $user->email }}" disabled="disabled">
                        </div>

                        <div class="form-item">
                            <label>{{ __('piclommerce::web.user_password') }}</label>
                            <input type="password" name="password" value="">
                            <div class="desc">{{ __('piclommerce::web.user_password_empty') }}</div>
                        </div>

                        <div class="form-item">
                            <label class="checkbox">
                                <input type="checkbox" name="newsletter" {!! ($user->newsletter)?'checked="checked"':'' !!}>
                                {{ __('piclommerce::web.user_newsletter_subscribe') }}
                            </label>
                            <div class="desc">
                                {{ __('piclommerce::web.user_newsletter_unsubscribe') }}
                            </div>
                        </div>

                        <button type="submit">
                            {{ __('piclommerce::web.register') }}
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection