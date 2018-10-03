
<div class="is-row">
    <div class="is-col">
        <div class="form-item">
            <label class="checkbox">
                <input type="checkbox" name="order_accept" value="1" {!! (!empty($data->order_accept))?'checked="checked"':'' !!}>
                {{ __('piclommerce::admin.order_status_accept') }} ?
            </label>
            <div class="desc">{{ __('piclommerce::admin.order_status_accept_desc') }}</div>
        </div>
    </div>
    <div class="is-col">
        <div class="form-item">
            <label class="checkbox">
                <input type="checkbox" name="order_refuse" value="1" {!! (!empty($data->order_refuse))?'checked="checked"':'' !!}>
                {{ __('piclommerce::admin.order_status_refuse') }} ?
            </label>
            <div class="desc">{{ __('piclommerce::admin.order_status_refuse_desc') }}</div>
        </div>
    </div>
</div>
<div class="is-row">
    <div class="is-col">
        <div class="form-item">
            <label for="form-name">{{ __('piclommerce::admin.order_status_name') }} ?</label>
            <input type="text" name="name" id="form-name" value="{{ $data->name }}">
        </div>
    </div>
    <div class="is-col">
        <div class="form-item">
            <label for="form-color">{{ __('piclommerce::admin.order_status_color') }}</label>
            <input type="text" name="color" value="{{ $data->color }}">
        </div>
    </div>
</div>


