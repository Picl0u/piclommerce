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
        <link href="/css/app.css" rel="stylesheet">

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        @if(!empty(setting('generals.analytics')))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('generals.analytics') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{ setting('generals.analytics') }}');
            </script>
        @endif
    </head>
    <body>
        <header>
            <div class="header-top">
                <div class="l-container">
                    <a href="/" class="logo">
                        <img src="{{ asset(setting('generals.logo')) }}" alt="{{ setting('generals.websiteName') }}" >
                    </a>
                    <div class="header-socials">
                        @if(!empty(setting('generals.facebook')))
                            <a href="{{ setting('generals.facebook') }}" target="_blank">
                                <i class="fa fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(!empty(setting('generals.twitter')))
                            <a href="{{ setting('generals.twitter') }}" target="_blank">
                                <i class="fa fa-twitter"></i>
                            </a>
                        @endif
                        @if(!empty(setting('generals.pinterest')))
                            <a href="{{ setting('generals.pinterest') }}" target="_blank">
                                <i class="fa fa-pinterest-p"></i>
                            </a>
                        @endif
                        @if(!empty(setting('generals.googlePlus')))
                            <a href="{{ setting('generals.googlePlus') }}" target="_blank">
                                <i class="fa fa-google-plus-g"></i>
                            </a>
                        @endif
                        @if(!empty(setting('generals.instagram')))
                            <a href="{{ setting('generals.instagram') }}" target="_blank">
                                <i class="fa fa-instagram"></i>
                            </a>
                        @endif
                        @if(!empty(setting('generals.youtube')))
                            <a href="{{ setting('generals.youtube') }}" target="_blank">
                                <i class="fa fa-youtube"></i>
                            </a>
                        @endif
                        @php $langs = array_diff(config('piclommerce.languages'),[config('app.locale')]); @endphp
                        @foreach($langs as $lang)
                            <a href="{{ route('change.language',['locale' => $lang]) }}">
                                {{ strtoupper($lang) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="header-bottom">
                <div class="l-container">
                    <div class="is-navbar-container">
                        <a href="#"
                           class="nav-toggle is-push-right-mobile is-shown-mobile icon-kube-menu"
                           data-kube="toggle"
                           data-target="#main-navigation"></a>
                        <div class="is-navbar is-hidden-mobile" id="main-navigation">
                            <nav class="main-navigation">
                                <ul>
                                    <li class="is-active">
                                        <a href="/">
                                            {{ __("piclommerce::web.navigation_home") }}
                                        </a>
                                    </li>
                                    <li class="shop-menu-parent">
                                        <a href="#">
                                            {{ __("piclommerce::web.navigation_shop") }}
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                        </a>
                                        <ul class="submenu shop-menu">
                                            <div class="is-row">
                                                <div class="is-col is-25 padding-left">
                                                    @php $products = navigationNewProduct(); @endphp
                                                    @if(count($products))
                                                        <span class="sub-title">{{ __("piclommerce::web.shop_new_product") }}</span>
                                                        <div class="new-product-slider">
                                                            @foreach($products as $product)
                                                                <div class="product-slide">
                                                                    @include("piclommerce::components.product", compact("product"))
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                </div>
                                                <div class="is-col is-70">
                                                    <ul class="is-row is-bricks is-col-stack-20 is-col-stack-4-mobile is-block">
                                                        {!! navigationShopCategories() !!}
                                                    </ul>
                                                </div>
                                            </div>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="{{ route('product.flash') }}">
                                            {{ __("piclommerce::web.navigation_flash") }}
                                        </a>
                                    </li>
                                    {!! navigationContents() !!}
                                    <li>
                                        <a href="{{ route('contact.index') }}">
                                            {{ __("piclommerce::web.navigation_contact") }}
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <nav class="is-push-right e-commerce-navigation">
                                <ul>
                                    <li>
                                        @if(Auth::user())
                                            @if(Auth::user()->role == 'user')
                                                <a data-kube="dropdown" data-target="#user-dropdown">
                                                    <i class="fa fa-user"></i>
                                                    {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                                                    <span class="caret is-down"></span>
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}">
                                                <i class="fa fa-user"></i>
                                            </a>
                                        @endif
                                    </li>
                                    <li>
                                        <a href="{{ route("whishlist.index") }}">
                                            <i class="fa fa-heart"></i>
                                            <span class="whishlist-count">{{ Cart::instance('whishlist')->count() }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cart.show') }}">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="shopping-count">{{ Cart::instance('shopping')->count() }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div id="user-dropdown" class="dropdown is-hidden">
            <a href="{{ route('user.account') }}">
                {{ __('piclommerce::web.user_my_account') }}
            </a>
            <a href="{{ route('user.infos') }}">
                {{ __('piclommerce::web.user_my_informations') }}
            </a>
            <a href="{{ route('user.addresses') }}">
                {{ __('piclommerce::web.user_my_addresses') }}
            </a>
            <a href="{{ route('order.index') }}">
                {{ __('piclommerce::web.user_my_orders') }}
            </a>
            <a href="{{ route('logout') }}">
                {{ __('piclommerce::web.user_logout') }}
            </a>
            <form class="logout-form" action="{{ route('logout') }}" method="POST">
                {{ csrf_field() }}
            </form>
        </div>

        <div id="content">
            @if(count($errors) > 0)
                @include("piclommerce::components.alert-error",['errors' => $errors])
            @endif
            @yield('content')
        </div>
        <footer>
            <div class="l-container">
                <div class="is-row">
                    <div class="is-col is-33">
                        <h5 class="footer-title">{{ __("piclommerce::web.about_store") }}</h5>
                        <div class="company-informations">
                            @if(setting("generals.address"))
                            <div class="infos">
                                <i class="fa fa-map-marker"></i>
                                <span>{{ setting("generals.address") }} {{ setting("generals.zipCode") }} {{ setting("generals.city") }}</span>
                            </div>
                            @endif
                            @if(setting("generals.phone"))
                                <div class="infos">
                                    <i class="fa fa-phone"></i>
                                    <span>{{ setting("generals.phone") }}</span>
                                </div>
                            @endif
                            @if(setting("generals.email"))
                                <div class="infos">
                                    <i class="fa fa-envelope"></i>
                                    <span>{{ setting("generals.email") }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="is-col is-33">
                        <div class="is-row is-col-stack-20 navigation-footer">
                            <div class="is-col is-50">
                                <h5 class="footer-title">{{ __("piclommerce::web.our_services") }}</h5>
                                <a href="/">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                    {{ __("piclommerce::web.navigation_home") }}
                                </a>
                                <a href="{{ route('order.index') }}">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                    {{ __("piclommerce::web.orders") }}
                                </a>
                                <a href="{{ route("cart.show") }}">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                    {{ __("piclommerce::web.order_cart") }}
                                </a>
                                <a href="{{ route("whishlist.index") }}">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                    {{ __("piclommerce::web.order_whishlist") }}
                                </a>
                            </div>
                            <div class="is-col is-50">
                                <h5 class="footer-title">{{ __("piclommerce::web.informations") }}</h5>
                                {!! footerNavigation() !!}
                                <a href="{{ route('contact.index') }}">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                    {{ __("piclommerce::web.navigation_contact") }}
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="is-col is-33">
                        <h5 class="footer-title">{{ __("piclommerce::web.newsletter_title") }}</h5>
                        <div class="newsletter">
                            <form method="post" action="{{ route("newsletter.register") }}">
                                <div class="form-item">
                                    <input type="email" required="required" name="email" placeholder="{{ __("piclommerce::web.email") }}" value="">
                                </div>
                                <button type="submit">
                                    {{ __("piclommerce::web.newsletter_button") }}
                                </button>
                                <div class="clear"></div>
                            </form>
                        </div>
                        <h5 class="footer-title">{{ __("piclommerce::web.follow_us") }}</h5>
                        <div class="footer-socials">
                            @if(!empty(setting('generals.facebook')))
                                <a href="{{ setting('generals.facebook') }}" target="_blank">
                                    <i class="fa fa-facebook-f"></i>
                                </a>
                            @endif
                            @if(!empty(setting('generals.twitter')))
                                <a href="{{ setting('generals.twitter') }}" target="_blank">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            @endif
                            @if(!empty(setting('generals.pinterest')))
                                <a href="{{ setting('generals.pinterest') }}" target="_blank">
                                    <i class="fa fa-pinterest-p"></i>
                                </a>
                            @endif
                            @if(!empty(setting('generals.googlePlus')))
                                <a href="{{ setting('generals.googlePlus') }}" target="_blank">
                                    <i class="fa fa-google-plus-g"></i>
                                </a>
                            @endif
                            @if(!empty(setting('generals.instagram')))
                                <a href="{{ setting('generals.instagram') }}" target="_blank">
                                    <i class="fa fa-instagram"></i>
                                </a>
                            @endif
                            @if(!empty(setting('generals.youtube')))
                                <a href="{{ setting('generals.youtube') }}" target="_blank">
                                    <i class="fa fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="l-container">
                    <div class="is-container">
                        <div>
                            &copy; {{ date("Y") }} {{ setting('generals.websiteName') }}
                        </div>
                        <div class="is-push-right">

                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <div class="remodal modal-cart" data-remodal-id="modal-cart">
            <div data-remodal-action="close" class="remodal-close"></div>
            <div class="modal-title">
                <i class="fa fa-check"></i>
                {{ __("piclommerce::web.cart_product_success") }}
            </div>
            <div class="is-row">
                <div class="is-col is-40 cart-product">
                    <div class="is-row">
                        <div class="is-col is-50 cart-product-image">
                        </div>
                        <div class="is-col is-50 cart-product-infos">
                            <div class="product-name"></div>
                            <div class="product-price"></div>
                            <div class="product-quantity">{{ __("piclommerce::web.cart_quantity") }} :<span></span></div>
                        </div>
                    </div>
                </div>
                <div class="is-col is-60 cart-infos">
                    <p class="total-count"></p>
                    <p class="transport"></p>
                    <p class="total"></p>
                    <div class="link">
                        <a href="#" data-remodal-action="close">{{ __("piclommerce::web.cart_continue_shopping") }}</a>
                        <a href="{{ route('cart.show') }}"><i class="fa fa-check"></i> {{ __("piclommerce::web.cart_order") }}</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
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
