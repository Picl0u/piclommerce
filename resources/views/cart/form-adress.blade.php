<form method="post" action="{{ route('cart.user.address.store') }}">
    {{ csrf_field() }}
    <label>{{ __('piclommerce::web.user_civility') }}</label>
    <div class="form-item form-checkboxes">
        <label class="is-checkbox">
            <input type="radio" name="gender" value="M" {!! ($user->gender == "M")?'checked="checked"':'' !!} >
            {{ __('piclommerce::web.user_civility_mr') }}
        </label>
        <label class="is-checkbox">
            <input type="radio" name="gender" value="Mme" {!! ($user->gender == "Mme")?'checked="checked"':'' !!}>
            {{ __('piclommerce::web.user_civility_mrs') }}
        </label>
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_firstname') }}</label>
        <input type="text" name="firstname" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_lastname') }}</label>
        <input type="text" name="lastname" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_address') }}</label>
        <input type="text" name="address" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_address_additional') }}</label>
        <input type="text" name="additional_address" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_zip_code') }}</label>
        <input type="text" name="zip_code" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_city') }}</label>
        <input type="text" name="city" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_phone') }}</label>
        <input type="text" name="phone" required="required" value="">
    </div>

    <div class="form-item">
        <label>{{ __('piclommerce::web.user_country') }}</label>
        <select name="country_id" id="country_id">
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {!! ($country->id == setting("orders.countryId"))?'selected="selected"':'' !!}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>

    </div>


    <div class="form-item">
        <label class="is-checkbox">
            <input type="checkbox" name="billing" checked="checked">
            {{ __('piclommerce::web.user_same_address') }}
        </label>
    </div>

    <button type="submit">
        {{ __('piclommerce::web.register') }}
    </button>

</form>