@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-picture-o"></i>
                    {{ __('piclommerce::admin.navigation_slider') }}
                    <span>{{ __('piclommerce::admin.add') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_personalize') }}</a>
                    <a href="{{ route("admin.sliders.index") }}">{{ __('piclommerce::admin.navigation_slider') }}</a>
                    <span>{{ __('piclommerce::admin.add') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.sliders.index") }}">
                <i class="fa fa-arrow-left"></i>
                {{ __('piclommerce::admin.return') }}
            </a>
            <a class="submit-form" href="#">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                {{ __('piclommerce::admin.save') }}
            </a>
            <div class="clear"></div>
        </div>
        <form class="admin-form" method="post" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            @include("piclommerce::admin.slider.form", compact('data'))
            <div class="form-item is-buttons">
                <button type="submit" class="button">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    {{ __('piclommerce::admin.save') }}
                </button>
            </div>
        </form>
    </div>

@endsection