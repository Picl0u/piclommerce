@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-cog"></i>
                    {{ __('piclommerce::admin.navigation_orders') }}
                    <span>{{ __('piclommerce::admin.navigation_configure') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_configure') }}</a>
                    <a href="{{ route("admin.sliders.index") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
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
        <form class="admin-form" method="post" action="{{ route('admin.settings.orders.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}


            <div class="is-row">

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="noAccount" value="1" {!! (!empty($data['noAccount']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_order_express') }}
                        </label>
                        <div class="desc">{{ __('piclommerce::admin.setting_order_express_desc') }}</div>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="orderAgain" value="1" {!! (!empty($data['orderAgain']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_order_again') }}
                        </label>
                        <div class="desc">{{ __('piclommerce::admin.setting_order_again_desc') }}</div>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_order_min_ammout') }}</label>
                        <input type="text" name="minAmmout" value="{{ $data['minAmmout'] }}">
                        <div class="desc">{{ __('piclommerce::admin.setting_order_min_ammout_desc') }}</div>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="stockBooked" value="1" {!! (!empty($data['stockBooked']))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.setting_order_stock') }}
                        </label>
                        <div class="desc">{{ __('piclommerce::admin.setting_order_stock_desc') }}</div>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_order_free') }}</label>
                        <input type="text" name="freeShippingPrice" value="{{ $data['freeShippingPrice'] }}">
                        <div class="desc">{!! __('piclommerce::admin.setting_order_free_desc') !!}</div>
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label form="form-countryId">{{ __('piclommerce::admin.setting_order_country') }}</label>
                        <select id="form-countryId" name="countryId">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {!! ($data['countryId'] == $country->id)?'selected="selected"':'' !!}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="is-col is-100">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_order_quantity_alert') }}</label>
                        <input type="text" name="productQuantityAlert" value="{{ $data['productQuantityAlert'] }}">
                        <div class="desc">{!! __('piclommerce::admin.setting_order_quantity_alert_desc') !!}</div>
                    </div>
                </div>

                <div class="is-col is-33">
                    <div class="form-item">
                        <label form="form-cgvId">{{ __('piclommerce::admin.setting_order_cgv') }}</label>
                        <select id="form-cgvId" name="cgvId">
                            @foreach($contents as $content)
                                <option value="{{ $content->id }}" {!! ($data['cgvId'] == $content->id)?'selected="selected"':'' !!}>
                                    {{ $content->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="is-col is-33">
                    <div class="form-item">
                        <label form="form-acceptId">{{ __('piclommerce::admin.setting_order_accept') }}</label>
                        <select id="form-acceptId" name="acceptId">
                            @foreach($contents as $content)
                                <option value="{{ $content->id }}" {!! ($data['acceptId'] == $content->id)?'selected="selected"':'' !!}>
                                    {{ $content->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="is-col is-33">
                    <div class="form-item">
                        <label form="form-refuseId">{{ __('piclommerce::admin.setting_order_refuse') }}</label>
                        <select id="form-refuseId" name="refuseId">
                            @foreach($contents as $content)
                                <option value="{{ $content->id }}" {!! ($data['refuseId'] == $content->id)?'selected="selected"':'' !!}>
                                    {{ $content->name }}
                                </option>
                            @endforeach
                        </select>
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