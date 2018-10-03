@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-cog"></i>
                    {{ __('piclommerce::admin.navigation_products') }}
                    <span>{{ __('piclommerce::admin.navigation_configure') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_configure') }}</a>
                    <a href="{{ route("admin.sliders.index") }}">{{ __('piclommerce::admin.navigation_products') }}</a>
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
        <form class="admin-form" method="post" action="{{ route('admin.settings.products.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-item">
                <label>{{ __('piclommerce::admin.setting_products_paginate') }}</label>
                <input type="text" name="paginate" value="{{ $data['paginate'] }}">
            </div>

            <div class="is-row">

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="commentEnable" value="1" {!! (!empty($data['commentEnable']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_products_comments') }}
                        </label>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="socialEnable" value="1" {!! (!empty($data['socialEnable']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_products_social') }}
                        </label>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="zoomEnable" value="1" {!! (!empty($data['zoomEnable']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_products_zoom') }}
                        </label>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="modalEnable" value="1" {!! (!empty($data['modalEnable']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_products_modal') }}
                        </label>
                    </div>
                </div>

            </div>
            <div class="form-item">
                <label>
                    {{ __('piclommerce::admin.setting_products_new') }}
                </label>
                <input type="text" name="new" value="{{ $data['new'] }}">
                <div class="desc">{{ __('piclommerce::admin.setting_products_new_desc') }}</div>
            </div>

            <div class="is-row">

                <div class="is-col">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_products_order_field') }}</label>
                        <select name="orderField">

                            <option value="order" {!! ($data['orderField'] == 'order')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_position') }}
                            </option>

                            <option value="price_ttc" {!! ($data['orderField'] == 'price_ttc')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_price_ttc') }}
                            </option>

                            <option value="created_at" {!! ($data['orderField'] == 'created_at')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_created_at') }}
                            </option>

                            <option value="updated_at" {!! ($data['orderField'] == 'updated_at')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_updated_at') }}
                            </option>

                            <option value="stock_available" {!! ($data['orderField'] == 'stock_available')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_stock_available') }}
                            </option>

                            <option value="reference" {!! ($data['orderField'] == 'reference')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_reference') }}
                            </option>

                        </select>
                        <div class="desc">{{ __('piclommerce::admin.setting_products_order_field_desc') }}</div>
                    </div>
                </div>

                <div class="is-col">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_products_order_dir') }}</label>
                        <select name="orderDirection">

                            <option value="asc" {!! ($data['orderField'] == 'asc')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_asc') }}
                            </option>

                            <option value="desc" {!! ($data['orderField'] == 'desc')?'selected="selected"':'' !!}>
                                {{ __('piclommerce::admin.setting_products_order_desc') }}
                            </option>

                        </select>
                        <div class="desc">{{ __('piclommerce::admin.setting_products_order_dir_desc') }}</div>
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