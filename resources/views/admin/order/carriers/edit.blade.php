@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-shopping-cart"></i>
                    {{ __('piclommerce::admin.navigation_orders') }} / {{ __("piclommerce::admin.navigation_carriers") }}
                    <span>{{ __('piclommerce::admin.edit') }} : {{ $data->name }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <a href="{{ route("admin.order.carriers.index") }}">{{ __('piclommerce::admin.navigation_carriers') }}</a>
                    <span>{{ __('piclommerce::admin.edit') }} {{ $data->name }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.order.carriers.index") }}">
                <i class="fa fa-arrow-left"></i>
                {{ __('piclommerce::admin.return') }}
            </a>
            <a class="submit-form" href="#">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                {{ __('piclommerce::admin.save') }}
            </a>
            <div class="clear"></div>
        </div>
        <form class="admin-form" method="post" action="{{ route('admin.order.carriers.update',['uuid' => $data->uuid]) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            @include("piclommerce::admin.order.carriers.form", compact('data'))
            <div class="form-item is-buttons">
                <button type="submit" class="button">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    {{ __('piclommerce::admin.save') }}
                </button>
            </div>
        </form>
    </div>

@endsection