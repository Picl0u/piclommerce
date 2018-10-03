@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.user_edit_address') }}
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
                        <h2>
                            {{ __('piclommerce::web.user_my_addresses') }}
                            <a href="{{ route('user.addresses') }}">
                                <i class="fa fa-chevron-left"></i>
                                {{ __('piclommerce::web.return') }}
                            </a>
                        </h2>
                    </div>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif

                    <form method="post" action="{{ route('user.addresses.update',['uuid' => $data->uuid]) }}">
                        @include("piclommerce::users.address.form", compact('data', 'countries'))
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection