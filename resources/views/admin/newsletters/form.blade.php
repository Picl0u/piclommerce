
<section id="infos">
    <div class="form-item">
        <label class="checkbox">
            <input type="checkbox" name="active" value="1" {!! (!empty($data->active))?'checked="checked"':'' !!}>
            {{ __('piclommerce::admin.active') }} ?
        </label>
    </div>
    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label>{{ __('piclommerce::admin.user_firstname') }}</label>
                <input type="text" name="firstname" value="{{ $data->firstname }}">
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label >{{ __('piclommerce::admin.user_lastname') }}</label>
                <input type="text" name="lastname" value="{{ $data->lastname }}">
            </div>
        </div>
    </div>
    <div class="form-item">
        <label>{{ __('piclommerce::admin.user_email') }}</label>
        <input type="email" name="email" value="{{ $data->email }}">
    </div>

</section>