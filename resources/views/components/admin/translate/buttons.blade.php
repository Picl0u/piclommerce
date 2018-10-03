{{ __('piclommerce::admin.translate') }} :
@foreach ($langs as $lang)
    <a href="#translate-modal" data-lang="{{ $lang }}">
        <i class="fa fa-plus"></i>
        {{ $lang }}
    </a>
@endforeach