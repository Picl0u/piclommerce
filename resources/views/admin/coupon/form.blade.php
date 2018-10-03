<div class="form-item">
    <label class="checkbox">
        <input type="checkbox" name="published" value="1" {!! (!empty($data->published))?'checked="checked"':'' !!}>
        {{ __('piclommerce::admin.online') }} ?
    </label>
</div>
<div class="is-row">
    <div class="is-col is-50">
        <div class="form-item">
            <label for="form-name">{{ __('piclommerce::admin.coupon_name') }}</label>
            <input type="text" name="name" id="form-name" value="{{ $data->name }}">
        </div>
    </div>
    <div class="is-col is-50">
        <div class="form-item">
            <label for="form-coupon">{{ __('piclommerce::admin.coupon_coupon') }}</label>
            <input type="text" name="coupon" value="{{ $data->coupon }}">
        </div>
    </div>
    <div class="is-col is-50">
        <div class="form-item">
            <label for="form-percent">{{ __('piclommerce::admin.coupon_percent') }}</label>
            <input type="text" name="percent" value="{{ $data->percent }}">
        </div>
    </div>
    <div class="is-col is-50">
        <div class="form-item">
            <label for="form-price">{{ __('piclommerce::admin.coupon_price') }}</label>
            <input type="text" name="price" value="{{ $data->price }}">
        </div>
    </div>
</div>
<div class="form-item">
    <label for="form-amount_min">{{ __('piclommerce::admin.coupon_price_min') }}</label>
    <input type="text" name="amount_min" value="{{ $data->amount_min }}">
</div>

<div class="is-row">
    <div class="is-col is-50">
        <div class="form-item">
            <label for="form-begin">{{ __('piclommerce::admin.coupon_begin') }}</label>
            <input type="text" name="begin" value="{{ $data->begin }}" class="datetime-picker">
            <div class="desc">{{ __('piclommerce::admin.coupon_optional') }}</div>
        </div>
    </div>
    <div class="is-col is-50">
        <div class="form-item">
            <label for="form-begin">{{ __('piclommerce::admin.coupon_end') }}</label>
            <input type="text" name="end" value="{{ $data->end }}" class="datetime-picker">
            <div class="desc">{{ __('piclommerce::admin.coupon_optional') }}</div>
        </div>
    </div>

    <div class="is-col is-50">
        <div class="form-item">
            <label>{{ __('piclommerce::admin.coupon_users') }}</label>
            <select name="users[]" multiple="multiple" class="multiple-select">
                @foreach($users as $user)
                    <?php
                    $selected="";
                    foreach ($data->CouponUsers as $cu) {
                        if($cu->user_id == $user->id){
                            $selected='selected="selected"';
                        }
                    }
                    ?>
                    <option value="{{ $user->id }}" {{ $selected }}>
                        {{ $user->email }} - {{ $user->firstname }} {{ $user->lastname }}
                    </option>
                @endforeach
            </select>
            <div class="desc">{{ __('piclommerce::admin.coupon_users_desc') }}</div>
        </div>
    </div>
    <div class="is-col is-50">
        <div class="form-item">
            <label>{{ __('piclommerce::admin.coupon_products') }}</label>
            <select name="products[]" multiple="multiple" class="multiple-select">
                @foreach($products as $product)
                    <?php
                    $selected="";
                    foreach ($data->CouponProducts as $cp) {
                        if($cp->product_id == $product->id){
                            $selected='selected="selected"';
                        }
                    }
                    ?>
                    <option value="{{ $product->id }}" {{ $selected }}>
                        {{ $product->name }} - Ref : {{ $product->reference }}
                    </option>
                @endforeach
            </select>
            <div class="desc">{{ __('piclommerce::admin.coupon_products_desc') }}</div>
        </div>
    </div>
</div>