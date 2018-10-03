<div class="is-row">
    <div class="is-col">
        <div class="form-item">
            <label class="checkbox">
                <input type="checkbox" name="online" value="1" {!! (!empty($data->online))?'checked="checked"':'' !!}>
                {{ __('piclommerce::admin.online') }}?
            </label>
        </div>
    </div>
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

<div class="is-row">
    <div class="is-col">
        <div class="form-item">
            <label for="form-name">{{ __('piclommerce::admin.user_email') }}</label>
            <input type="email" name="email" value="{{ $data->email }}">
        </div>
    </div>
    <div class="is-col">
        <div class="form-item">
            <label for="form-name">{{ __('piclommerce::admin.user_password') }}</label>
            <input type="password" name="password" value="">
            <div class="desc">{{ __('piclommerce::admin.user_password_desc') }}</div>
        </div>
    </div>
    <div class="is-col">
        <div class="form-item">
            <label>Rôle</label>
            <select name="role_id">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {!! ($data->role_id == $role->id)?'selected="selected"':'' !!}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
