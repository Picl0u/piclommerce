<section id="infos">
    <div class="form-item">
        <label class="checkbox">
            <input type="checkbox" name="published" value="1" {!! (!empty($data->published))?'checked="checked"':'' !!}>
            {{ __('piclommerce::admin.online') }} ?
        </label>
    </div>

    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label>{{ __('piclommerce::admin.orders_user') }}</label>
                <select name="user_id" class="multiple-select">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {!! ($data->user_id == $user->id)?'selected="selected"':'' !!}>
                            {{ $user->email }} - {{ $user->firstname }} {{ $user->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label>{{ __('piclommerce::admin.coupon_products') }}</label>
                <select name="product_id" class="multiple-select">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"  {!! ($data->product_id == $product->id)?'selected="selected"':'' !!}>
                            {{ $product->name }} - Ref : {{ $product->reference }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-item">
        <label >{{ __('piclommerce::admin.message') }}</label>
        <textarea cols="0" rows="0" name="comment" >{{ $data->comment }}</textarea>
    </div>

</section>
