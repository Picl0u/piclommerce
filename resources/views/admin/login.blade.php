<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {!! SEO::generate() !!}

        <!-- Styles -->
        <link href="/css/admin.css" rel="stylesheet">

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body>
        <div class="login-section">
            <div class="is-row is-gapless">
                <div class="is-col is-40 center-form">
                    <div class="login-container">
                        <h1>
                            {{ __("piclommerce::admin.login_title") }}
                        </h1>
                        @if(count($errors) > 0)
                            @include("piclommerce::components.alert-error",['errors' => $errors])
                        @endif
                        <form method="post" action="{{ route("login") }}">
                            {{ csrf_field() }}

                            <div class="form-item">
                                <label for="form-email">{{ __("piclommerce::admin.login_email") }}</label>
                                <input id="form-email" type="email" name="email" required="required">
                            </div>

                            <div class="form-item">
                                <label for="form-password">{{ __("piclommerce::admin.login_password") }}</label>
                                <input id="form-password" type="password" name="password" required="required">
                            </div>

                            <div class="form-item">
                                <label class="is-checkbox">
                                    <input type="checkbox" name="remember" id="form-remember">
                                    {{ __("piclommerce::admin.login_remember") }}
                                </label>
                            </div>

                            <div class="form-item is-buttons">
                                <button class="button">{{ __("piclommerce::admin.login_title") }}</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="is-col is-60 center-form background-color">
                    <div class="login-container">
                        <h1>{{ __("piclommerce::admin.login_welcome") }}</h1>
                        <h2 class="text-uppercase">{{ __("piclommerce::admin.login_welcome_login") }}</h2>
                        <br>
                        <hr>
                        <br>
                        <p class="lead">
                            {{ __("piclommerce::admin.login_welcome_desc") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script src="/js/admin.js"></script>
        @if(Session::has('success'))
            <script type="text/javascript">
                toastr.success('{{ Session::get('success') }}');
            </script>
        @endif
        @if(Session::has('error'))
            <script type="text/javascript">
                toastr.error('{{ Session::get('error') }}');
            </script>
        @endif
    </body>
</html>
