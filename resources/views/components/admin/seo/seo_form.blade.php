<div class="seo-preview">
    <div class="seo-preview-title">
        <i class="fa fa-eye" aria-hidden="true"></i>
        Aperçu de l’extrait
    </div>
    <div class="seo-preview-google is-shadow-2">
        <div class="seo-title">
            <span></span> - {{ __("piclommerce::admin.seo_website") }}
        </div>
        <div class="seo-url">
            {{ str_replace("https://","",str_replace("http://","",url('/'))) }} &gt; <span></span>
        </div>
        <div class="seo-description" data-text="{{ __("piclommerce::admin.seo_preview_no_description") }}">
            {{ __("piclommerce::admin.seo_preview_no_description") }}
        </div>
    </div>
</div>

<div class="form-item">
    <label for="form-seo-keywords">{{ __('piclommerce::admin.seo_keywords') }}</label>
    <input type="text" id="form-seo-keywords" name="seo_keywords" value="{{ $data->seo_keywords }}">
    <div class="desc">{{ __('piclommerce::admin.seo_keywords_desc') }}</div>
</div>

<div class="form-item">
    <label for="form-slug">{{ __('piclommerce::admin.seo_slug') }}</label>
    <input type="text" name="slug" value="{{ $data->slug }}">
    <div class="desc">{{ __('piclommerce::admin.seo_slug_desc') }}</div>
</div>

<div class="form-item">
    <label for="form-seo-title">{{ __('piclommerce::admin.seo_title') }}</label>
    <input type="text" id="form-seo-title" name="seo_title" value="{{ $data->seo_title }}">
</div>

<div class="form-item">
    <label for="form-seo-description">{{ __('piclommerce::admin.seo_description') }}</label>
    <textarea cols="0" rows="0" name="seo_description" id="form-seo-description">{{ $data->seo_description }}</textarea>
</div>