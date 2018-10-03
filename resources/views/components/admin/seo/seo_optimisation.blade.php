<div class="seo-notifications">
    <h2>{{ __('piclommerce::admin.seo_notification_title') }}</h2>
    <div class="statut">

        <div class="problem">
            <h3>
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                {{ __('piclommerce::admin.seo_notification_problem') }}
            </h3>
            <ul class="is-unstyled">
                <li class="seo-keywords-no-keywords">
                    <span class="label warning"></span>
                    {{ __('piclommerce::admin.seo_no_keywords') }}
                </li>
                <li class="seo-keywords-no-title">
                    <span class="label warning"></span>
                    {{ __('piclommerce::admin.seo_keywords') }} <strong>« <span class="keyword"></span> »</strong> {{ __('piclommerce::admin.seo_keywords_no_title') }}
                </li>
                <li class="seo-keywords-no-url">
                    <span class="label warning"></span>
                    {{ __('piclommerce::admin.seo_keywords') }} <strong>« <span class="keyword"></span> »</strong> {{ __('piclommerce::admin.seo_keywords_no_url') }}
                </li>
                <li class="seo-keywords-no-content">
                    <span class="label warning"></span>
                    {{ __('piclommerce::admin.seo_keywords') }} {{ __('piclommerce::admin.seo_keywords_no_content') }}
                </li>
                <li class="seo-keywords-no-description">
                    <span class="label warning"></span>
                   {{ __('piclommerce::admin.seo_no_description') }}
                </li>
                <li class="seo-no-image">
                    <span class="label warning"></span>
                    {{ __('piclommerce::admin.seo_no_image') }}
                </li>
                <li class="seo-no-content-length">
                    <span class="label warning"></span>
                    Le texte contient 13 mots. Vous êtes bien en dessous du minimum recommandé de 300 mots. Ajoutez plus de contenu en relation avec le sujet.
                </li>
            </ul>
            <hr>
        </div>
        <div class="good">
            <h3>
                <i class="fa fa-check" aria-hidden="true"></i>
                {{ __('piclommerce::admin.seo_notification_good') }}
            </h3>
            <ul class="is-unstyled">
                <li class="seo-ok-title">
                    <span class="label success"></span>
                    {{ __('piclommerce::admin.seo_ok_title') }}
                </li>
                <li class="seo-ok-url">
                    <span class="label success"></span>
                    {{ __('piclommerce::admin.seo_ok_url') }}
                </li>
                <li class="seo-ok-content">
                    <span class="label success"></span>
                    {{ __('piclommerce::admin.seo_ok_content') }}
                </li>
                <li class="seo-ok-description">
                    <span class="label success"></span>
                    {{ __('piclommerce::admin.seo_ok_description') }}
                </li>
            </ul>
        </div>
    </div>
</div>