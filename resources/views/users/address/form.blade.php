{{ csrf_field() }}
<label>{{ __('piclommerce::web.user_civility') }}</label>
<div class="form-item form-checkboxes">
    <label class="is-checkbox">
        <input type="radio" name="gender" value="M" {!! ($data->gender == "M")?'checked="checked"':'' !!} >
        {{ __('piclommerce::web.user_civility_mr') }}
    </label>
    <label class="is-checkbox">
        <input type="radio" name="gender" value="Mme" {!! ($data->gender == "Mme")?'checked="checked"':'' !!}>
        {{ __('piclommerce::web.user_civility_mrs') }}
    </label>
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_firstname') }}</label>
    <input type="text" name="firstname" required="required" value="{{ $data->firstname }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_lastname') }}</label>
    <input type="text" name="lastname" required="required" value="{{ $data->lastname }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_address') }}</label>
    <input type="text" name="address" required="required" value="{{ $data->address }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_address_additional') }}</label>
    <input type="text" name="additional_address" value="{{ $data->additional_address }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_zip_code') }}</label>
    <input type="text" name="zip_code" required="required" value="{{ $data->zip_code }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_city') }}</label>
    <input type="text" name="city" required="required" value="{{ $data->city }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_phone') }}</label>
    <input type="text" name="phone" required="required" value="{{ $data->phone }}">
</div>

<div class="form-item">
    <label>{{ __('piclommerce::web.user_country') }}</label>
    <select name="country_id" id="country_id">
        @foreach($countries as $country)
            <?php
                $selected="";
                if(!empty($data->country_id)) {
                    if($country->id == $data->country_id){
                        $selected='selected="selected"';
                    }
                } else{
                    if($country->id == setting('orders.countryId')){
                        $selected='selected="selected"';
                    }
                }
            ?>
            <option value="{{ $country->id }}" {{ $selected }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>

</div>


<div class="form-item">
    <label class="is-checkbox">
        <?php
            $checked='checked';
            if(!empty($data->address)){
                $checked='';
                if(!empty($data->billing)){
                    $checked='checked';
                }
            }
        ?>
        <input type="checkbox" name="billing" {{ $checked }}>
        {{ __('piclommerce::web.user_same_address') }}
    </label>
</div>

<button type="submit">
    {{ __('piclommerce::web.register') }}
</button>