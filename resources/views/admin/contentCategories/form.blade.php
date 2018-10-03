
<div class="form-item">
    <label class="checkbox">
        <input type="checkbox" name="on_footer" value="1" {!! (!empty($data->on_footer))?'checked="checked"':'' !!}>
        {{ __('piclommerce::admin.content_categories_on_footer') }}
        <div class="desc">{{ __('piclommerce::admin.content_categories_on_footer_desc') }}</div>
    </label>
</div>
<div class="form-item">
    <label for="form-name">{{ __('piclommerce::admin.content_categories_name') }} ?</label>
    <input type="text" name="name" id="form-name" value="{{ $data->name }}">
</div>


