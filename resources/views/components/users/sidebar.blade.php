<nav class="sidebar-navigation">
    <ul>
        <li>
            <a href="{{ route('user.account') }}">
                {{ __('piclommerce::web.user_my_account') }}
            </a>
        </li>
        <li>
            <a href="{{ route('user.infos') }}">
                {{ __('piclommerce::web.user_my_informations') }}
            </a>
        </li>
        <li>
            <a href="{{ route('user.addresses') }}">
                {{ __('piclommerce::web.user_my_addresses') }}
            </a>
        </li>
        <li>
            <a href="{{ route('order.index') }}">
                {{ __('piclommerce::web.user_my_orders') }}
            </a>
        </li>
        <li>
            <a href="{{ route('whishlist.index') }}">
                {{ __('piclommerce::web.user_my_whishlist') }}
            </a>
        </li>
        <li class="user-logout">
            <a href="{{ route('logout') }}">
                {{ __('piclommerce::web.user_logout') }}
            </a>
            <form class="logout-form" action="{{ route('logout') }}" method="POST">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</nav>