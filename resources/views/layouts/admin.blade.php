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
        @yield('style')
        <link type="text/css" rel="stylesheet" href="{{asset('css/admin.css')}}"/>

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <header>
            <div class="is-navbar-container">
                <div class="is-brand">
                    <b class="is-logo">{{ setting("generals.websiteName") }}</b>
                    <a href="#"
                       class="nav-toggle is-push-right-mobile is-shown-mobile icon-kube-menu"
                       data-kube="toggle"
                       data-target="#main-navigation"></a>
                </div>
                <div class="is-navbar is-hidden-mobile">
                    <nav class="is-push-right">
                        <ul>
                            <li>
                                <a href="/" target="_blank">
                                    {{ __("piclommerce::admin.navigation_see_website") }}
                                </a>
                            </li>
                            <li>
                                <a data-kube="dropdown" data-target="#user-dropdown">
                                    {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}
                                    <span class="caret is-down"></span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div id="user-dropdown" class="dropdown is-hidden">
                        <a href="{{ route('logout') }}">
                            {{ __('piclommerce::web.user_logout') }}
                        </a>
                        <form class="logout-form" action="{{ route('logout') }}" method="POST">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="is-row">
            <div class="is-col col-navigation">
                <nav class="main-navigation is-hidden-mobile" id="main-navigation">
                    @include("piclommerce::components.admin.navigation")
                </nav>
            </div>
            <div class="is-col col-content">
                <div id="content">
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error",['errors' => $errors])
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>

        <div class="remodal" data-remodal-id="remodal" data-remodal-options="hashTracking:false">
            <span data-remodal-action="close" class="remodal-close"></span>
            <div class="forImg"></div>
        </div>

        <footer>
            <div class="l-container">
                <p>
                    Développé avec <i class="fa fa-heart"></i> par
                    <a href="{{ config("piclommerce.authorUrl") }}" target="_blank">
                        {{ config("piclommerce.author") }}
                    </a> -
                    {{ config("piclommerce.apiVersion") }}
                </p>
            </div>
        </footer>

        <!-- JavaScript -->
        <script type="text/javascript" src="{{asset('js/admin.js')}}"></script>
        @stack('scripts')
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
