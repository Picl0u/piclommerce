@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-picture-o"></i>
                    {{ __('piclommerce::admin.navigation_slider') }}
                    <span>{{ __('piclommerce::admin.position') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_personalize') }}</a>
                    <a href="{{ route('admin.sliders.index') }}">{{ __('piclommerce::admin.navigation_slider') }}</a>
                    <span>{{ __("piclommerce::admin.position") }}</span>
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
            <div class="clear"></div>
        </div>

        <div class="nested-section" data-url="{{ route('admin.sliders.positions.store') }}">
            {!!
                nestableExtends($datas)
                ->firstUlAttr('class', 'sortable')
                ->liSortable()
                ->renderAsHtml()
            !!}
        </div>
    </div>

@endsection