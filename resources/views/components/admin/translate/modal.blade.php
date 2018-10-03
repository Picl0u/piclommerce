<div class="remodal modal-translate" data-remodal-id="translate-modal" data-remodal-options="hashTracking: false">
    <div data-remodal-action="close" class="remodal-close"></div>

    <form method="post" class="modal-form" action="" data-action="{{ $action }}">

        <h1>{{ __('piclommerce::admin.translate') }} : <span class="lang"></span></h1>

        {{ csrf_field() }}
        <div class="form-model">
            {!! $form !!}
        </div>
        <div class="modal-actions">
            <button data-remodal-action="cancel" class="remodal-cancel">{{ __('piclommerce::admin.cancel') }}</button>
            <button data-remodal-action="confirm" class="remodal-confirm">{{ __('piclommerce::admin.save') }}</button>
        </div>

    </form>

</div>