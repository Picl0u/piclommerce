@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-cog"></i>
                    {{ __('piclommerce::admin.navigation_slider') }}
                    <span>{{ __('piclommerce::admin.navigation_configure') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_configure') }}</a>
                    <a href="{{ route("admin.sliders.index") }}">{{ __('piclommerce::admin.navigation_slider') }}</a>
                    <span>{{ __('piclommerce::admin.add') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a class="submit-form" href="#">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                {{ __('piclommerce::admin.save') }}
            </a>
            <div class="clear"></div>
        </div>
        <form class="admin-form" method="post" action="{{ route('admin.settings.slider.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="is-row">
                <div class="is-col">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="arrows" value="1" {!! (!empty($data['arrows']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_slider_arrows') }}
                        </label>
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="dots" value="1" {!! (!empty($data['dots']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_slider_dots') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="is-row">

                <div class="is-col">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_slider_type') }}</label>
                        <select name="type">
                            <option value="boxed" {!! ($data['type'] == 'boxed')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_slider_type_boxed') }}
                            </option>
                            <option value="fullwidth" {!! ($data['type'] == 'fullwidth')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_slider_type_fullwidth') }}
                            </option>
                            <option value="fullscreen" {!! ($data['type'] == 'fullscreen')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_slider_type_fullscreen') }}
                            </option>
                        </select>
                        <div class="desc">{{ __('piclommerce::admin.slider_position_help') }}</div>
                    </div>
                </div>

                <div class="is-col">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_slider_transition') }}</label>
                        <select name="transition">
                            <option value="fade" {!! ($data['transition'] == 'fade')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_slider_transition_fade') }}
                            </option>
                            <option value="slide" {!! ($data['transition'] == 'slide')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_slider_transition_slide') }}
                            </option>
                        </select>
                        <div class="desc">{{ __('piclommerce::admin.slider_position_help') }}</div>
                    </div>
                </div>

            </div>

            <div class="is-row">

                <div class="is-col">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_slider_duration') }}</label>
                        <input type="text" name="slideDuration" value="{{ $data['slideDuration'] }}">
                        <div class="desc">{{ __('piclommerce::admin.setting_slider_duration_desc') }}</div>
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_slider_duration_transition') }}</label>
                        <input type="text" name="transitionDuration" value="{{ $data['transitionDuration'] }}">
                        <div class="desc">{{ __('piclommerce::admin.setting_slider_duration_transition_desc') }}</div>
                    </div>
                </div>
            </div>


            <div class="form-item is-buttons">
                <button type="submit" class="button">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    {{ __('piclommerce::admin.save') }}
                </button>
            </div>
        </form>
    </div>

@endsection