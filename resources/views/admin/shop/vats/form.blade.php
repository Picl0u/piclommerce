<section id="infos">
    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label for="form-name">{{ __('piclommerce::admin.shop_vat_name') }}</label>
                <input type="text" name="name" id="form-name" value="{{ $data->name }}">
                <div class="desc">
                    {{ __('piclommerce::admin.shop_vat_name_desc') }}
                </div>
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label for="form-percent">{{ __('piclommerce::admin.shop_vat_percent') }}</label>
                <div class="is-append">
                    <input type="text" name="percent" id="form-percent" value="{{ $data->percent }}">
                    <span>%</span>
                </div>
                <div class="desc">
                    {{ __('piclommerce::admin.shop_vat_percent_desc') }}
                </div>
            </div>
        </div>
    </div>
</section>
