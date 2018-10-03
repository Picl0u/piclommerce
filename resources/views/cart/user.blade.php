@extends("piclommerce::layouts.app")

@section("content")
    <div class="order-process">
        <div class="l-container">
            <div class="is-row">
                <div class="is-col is-60 process-container">
                    <h1>1. {{ __("piclommerce::web.user_personal_informations") }}</h1>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif
                    <nav class="tabs" data-kube="tabs">
                        @if(!empty(setting('orders.noAccount')))
                            <a href="#order-express" class="is-active">
                                {{ __('piclommerce::web.user_order_guest') }}
                            </a>
                            <a href="#order-connect">
                                {{ __('piclommerce::web.user_login') }}
                            </a>
                        @else
                            <a href="#order-connect" class="is-active">
                                {{ __('piclommerce::web.user_login') }}
                            </a>
                            <a href="#order-register">
                                {{ __('piclommerce::web.user_create_account') }}
                            </a>
                        @endif
                    </nav>
                    @if(!empty(setting('orders.noAccount')))
                        <section id="order-express">
                            <form method="post" action="{{ route("cart.user.express") }}">
                                {{ csrf_field() }}

                                <label>{{ __('piclommerce::web.user_civility') }}</label>
                                <div class="form-item form-checkboxes">
                                    <label class="is-checkbox">
                                        <input type="radio" name="express_gender" value="M" checked="checked">
                                        {{ __('piclommerce::web.user_civility_mr') }}
                                    </label>
                                    <label class="is-checkbox">
                                        <input type="radio" name="express_gender" value="Mme">
                                        {{ __('piclommerce::web.user_civility_mrs') }}
                                    </label>
                                </div>
                                <div class="form-item">
                                    <label for="express_firstname">{{ __('piclommerce::web.user_firstname') }}</label>
                                    <input type="text" id="express_firstname" required="required" name="express_firstname" value="">
                                </div>
                                <div class="form-item">
                                    <label for="express_lastname">{{ __('piclommerce::web.user_lastname') }}</label>
                                    <input type="text" id="express_lastname" required="required" name="express_lastname" value="">
                                </div>
                                <div class="form-item">
                                    <label for="express_email">{{ __('piclommerce::web.user_email') }}</label>
                                    <input type="email" id="express_email" required="required" name="express_email" value="">
                                </div>

                                <p>
                                    <strong>{{ __('piclommerce::web.user_create_account_optional') }}</strong><br>
                                    {{ __('piclommerce::web.user_create_account_optional_desc') }}
                                </p>

                                <div class="form-item">
                                    <label for="express_password">
                                        {{ __('piclommerce::web.user_password') }} -
                                        {{ __('piclommerce::web.optional') }}</label>
                                    <input type="password" id="express_password" required="required" name="express_password" value="">
                                </div>

                                <div class="form-item">
                                    <label class="checkbox">
                                        <input type="checkbox" name="express_newsletter" checked="checked">
                                        {{ __('piclommerce::web.user_newsletter_subscribe') }}
                                    </label>
                                    <div class="desc">
                                        {{ __('piclommerce::web.user_newsletter_unsubscribe') }}
                                    </div>
                                </div>

                                <button type="submit">
                                    {{ __('piclommerce::web.continue') }}
                                </button>
                            </form>

                        </section>
                    @endif
                    <section id="order-connect">
                        @include('piclommerce::components.users.login')
                    </section>
                    @if(empty(setting('orders.noAccount')))
                        <section id="order-register">
                            @include('piclommerce::components.users.register')
                        </section>
                    @endif

                    <div class="timeline">
                        <h2>2. {{ __("piclommerce::web.cart_address") }}</h2>
                    </div>

                    <div class="timeline">
                        <h2>3. {{ __("piclommerce::web.cart_shipping_method") }}</h2>
                    </div>

                    <div class="timeline">
                        <h2>4. {{ __("piclommerce::web.cart_payment") }}</h2>
                    </div>
                </div>
                <div class="is-col is-40">
                    @include('piclommerce::components.cart-resume')
                </div>
            </div>
        </div>
    </div>
@endsection