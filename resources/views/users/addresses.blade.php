@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>
                {{ __('piclommerce::web.user_my_addresses') }}
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
                            <a href="{{ route('user.addresses.create') }}">
                                <i class="fa fa-plus"></i>
                                {{ __('piclommerce::web.add') }}
                            </a>
                        </h2>
                    </div>
                    @if(count($errors) > 0)
                        @include("piclommerce::components.alert-error")
                    @endif

                    <div class="addresses is-row">
                        @foreach($addresses as $address)
                            <div class="is-col is-33 address">
                                <strong>{{ $address->firstname }} {{ $address->lastname }}</strong><br>
                                {{ $address->address }} {{ $address->additional_address }}<br>
                                {{ $address->zip_code }} {{ $address->city }}<br>
                                {{ $address->Country->name }}<br>
                                {{ __('piclommerce::web.user_phone') }} : {{ $address->phone }}
                                <div class="link">

                                    <a href="{{ route('user.addresses.edit',['uuid' => $address->uuid]) }}">
                                        <i class="fa fa-pencil"></i>
                                        {{ __('piclommerce::web.edit') }}
                                    </a>

                                    <a href="{{ route('user.addresses.delete',['uuid' => $address->uuid]) }}"
                                       class="delete-confirm"
                                    >
                                        <i class="fa fa-trash"></i>
                                        {{ __('piclommerce::web.delete') }}
                                        <span class="delete-infos"
                                              data-title="{{ __('piclommerce::web.warning') }}"
                                              data-message="{{ __('piclommerce::web.user_delete_message') }}"
                                              data-confirm="{{ __('piclommerce::web.confirm') }}"
                                              data-cancel="{{ __('piclommerce::web.cancel') }}"
                                        ></span>
                                    </a>

                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection