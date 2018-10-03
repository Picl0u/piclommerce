<div class="alert is-error" data-kube="alert">
    <h4>{{ __("piclommerce::admin.warning") }}</h4>
    <p>
        @foreach($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </p>
    <span class="close is-small" data-type="close"></span>
</div>