<ul>
    <li {!! (Route::current()->getName() == 'admin.dashboard')?'class="is-active"':'' !!}>
        <a href="{{ route("admin.dashboard") }}">
            <i class="fa fa-home"></i>
            {{ __('piclommerce::admin.navigation_dashboard') }}
        </a>
    </li>
    <li class="navigation-title">
        {{ __("piclommerce::admin.navigation_sale") }}
    </li>
    <li>
        <a href="#">
            <i class="fa fa-shopping-cart"></i>
            {{ __("piclommerce::admin.navigation_orders") }}
            <span class="caret is-left"></span>
        </a>
        <ul>
            <li>
                <a href="{{ route("admin.orders.orders.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_orders") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.orders.orders.invoices") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_invoices") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.order.carriers.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_carriers") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.order.countries.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_countries") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.order.status.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_status") }}
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-book"></i>
            {{ __("piclommerce::admin.navigation_catalog") }}
            <span class="caret is-left"></span>
        </a>
        <ul>
            <li>
                <a href="{{ route("admin.shop.products.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_products") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.shop.comments.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_comments") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.shop.categories.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_categories") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.shop.vats.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_vat") }}
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{ route("admin.coupon.index") }}">
            <i class="fa fa-ticket"></i>
            {{ __('piclommerce::admin.navigation_promotional') }}
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-users"></i>
            {{ __("piclommerce::admin.navigation_customers") }}
            <span class="caret is-left"></span>
        </a>
        <ul>
            <li>
                <a href="{{ route("admin.users.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_customers") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.addresses.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_addresses") }}
                </a>
            </li>
        </ul>
    </li>
    <li class="navigation-title">
        {{ __("piclommerce::admin.navigation_personalize") }}
    </li>
    <li {!! (Route::current()->getName() == 'admin.pages.categories.index')?'class="is-active"':'' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            {{ __("piclommerce::admin.navigation_pages") }}
            <span class="caret is-left"></span>
        </a>
        <ul>
            <li>
                <a href="{{ route("admin.pages.contents.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_contents") }}
                </a>
            </li>
            <li>
                <a href="{{ route("admin.pages.categories.index") }}">
                    <i class="fa fa-circle-o"></i>
                    {{ __("piclommerce::admin.navigation_sections") }}
                </a>
            </li>
        </ul>
    </li>
    <li {!! (Route::current()->getName() == 'admin.sliders.index')?'class="is-active"':'' !!}>
        <a href="{{ route('admin.sliders.index') }}">
            <i class="fa fa-picture-o"></i>
            {{ __('piclommerce::admin.navigation_slider') }}
        </a>
    </li>
    <li {!! (Route::current()->getName() == 'admin.banner.index')?'class="is-active"':'' !!}>
        <a href="{{ route('admin.banner.index') }}">
            <i class="fa fa-ticket"></i>
            {{ __('piclommerce::admin.navigation_banner') }}
        </a>
    </li>
    <li>
        <a href="{{ route("admin.newsletter.index") }}">
            <i class="fa fa-envelope-o"></i>
            {{ __('piclommerce::admin.navigation_newsletter') }}
        </a>
    </li>
    <li class="navigation-title">
        {{ __('piclommerce::admin.navigation_configure') }}
    </li>
    <li>
        <a href="{{ route("admin.settings.slider") }}">
            <i class="fa fa-cog"></i>
            {{ __('piclommerce::admin.navigation_slider') }}
        </a>
    </li>
    <li>
        <a href="{{ route("admin.settings.orders") }}">
            <i class="fa fa-cog"></i>
            {{ __('piclommerce::admin.navigation_orders') }}
        </a>
    </li>
    <li>
        <a href="{{ route("admin.settings.products") }}">
            <i class="fa fa-cog"></i>
            {{ __('piclommerce::admin.navigation_products') }}
        </a>
    </li>
    <li>
        <a href="{{ route("admin.settings.slider") }}">
            <i class="fa fa-cog"></i>
            {{ __('piclommerce::admin.navigation_slider') }}
        </a>
    </li>
    <li>
        <a href="{{ route("admin.admin.index") }}">
            <i class="fa fa-users"></i>
            {{ __('piclommerce::admin.navigation_administrators') }}
        </a>
    </li>
    <li>
        <a href="{{ route("admin.settings.generals") }}">
            <i class="fa fa-cog"></i>
            {{ __('piclommerce::admin.navigation_generals_settings') }}
        </a>
    </li>
</ul>