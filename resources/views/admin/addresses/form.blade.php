<div class="form-item">
    <label form="form-user_id">{{ __('piclommerce::admin.addresses_custommer') }}</label>
    <select id="form-user_id" name="user_id">
        @foreach($users as $user)
            <option value="{{ $user->id }}" {!! ($data->user_id == $user->id)?'selected="selected"':'' !!}>
                {{ $user->firstname }} {{ $user->lastname }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-item form-checkboxes">
    <label class="is-checkbox">
        <input type="radio" name="gender" value="M" {!! ($data->newsletter == "M")?'checked="checked"':'' !!}>
        {{ __('piclommerce::admin.user_mister') }}
    </label>
    <label class="is-checkbox">
        <input type="radio" name="gender" value="Mme" {!! ($data->newsletter == "Mme")?'checked="checked"':'' !!}>
        {{ __('piclommerce::admin.user_mrs') }}
    </label>
</div>

<div class="is-row">
    <div class="is-col">
        <div class="form-item">
            <label for="form-name">{{ __('piclommerce::admin.user_firstname') }}</label>
            <input type="text" name="firstname" value="{{ $data->firstname }}">
        </div>
    </div>
    <div class="is-col">
        <div class="form-item">
            <label for="form-name">{{ __('piclommerce::admin.user_lastname') }}</label>
            <input type="text" name="lastname" value="{{ $data->lastname }}">
        </div>
    </div>
</div>

<div class="form-item">
    <label for="form-address">{{ __('piclommerce::admin.address') }}</label>
    <input type="text" name="address" value="{{ $data->address }}">
</div>

<div class="form-item">
    <label for="form-zip_code">{{ __('piclommerce::admin.addresses_zip_code') }}</label>
    <input type="text" name="zip_code" value="{{ $data->zip_code }}">
</div>

<div class="form-item">
    <label for="form-city">{{ __('piclommerce::admin.addresses_city') }}</label>
    <input type="text" name="city" value="{{ $data->city }}">
</div>

<div class="form-item">
    <label for="form-city">{{ __('piclommerce::admin.addresses_phone') }}</label>
    <input type="text" name="phone" value="{{ $data->phone }}">
</div>

<div class="form-item">
    <label form="form-country_id">{{ __('piclommerce::admin.addresses_country') }}</label>
    <select id="form-country_id" name="country_id">
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
    <label class="checkbox">
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
            {{ __('piclommerce::admin.addresses_billing') }}
    </label>
</div>
