@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-cog"></i>
                    {{ __('piclommerce::admin.navigation_generals_settings') }}
                    <span>{{ __('piclommerce::admin.add') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_configure') }}</a>
                    <a href="{{ route("admin.sliders.index") }}">{{ __('piclommerce::admin.navigation_generals_settings') }}</a>
                    <span>{{ __('piclommerce::admin.add') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a class="submit-form" href="#">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                {{ __('piclommerce::admin.save') }}
            </a>
            <div class="clear"></div>
        </div>
        <form class="admin-form" method="post" action="{{ route('admin.settings.generals.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}

            <nav class="tabs" data-kube="tabs" data-equal="true">
                <a href="#infos" class="is-active">{{ __('piclommerce::admin.informations') }}</a>
                <a href="#contact">Contact</a>
                <a href="#invoice">Facture</a>
                <a href="#socials">Réseaux Sociaux</a>
                <a href="#seo">{{ __('piclommerce::admin.seo') }}</a>
            </nav>

            <section id="infos">

                <div class="form-item">
                    <label>{{ __('piclommerce::admin.setting_website_name') }}</label>
                    <input type="text" name="websiteName" value="{{ $data['websiteName'] }}">
                </div>

                <div class="form-item">
                    <label>{{ __('piclommerce::admin.setting_website_logo') }}</label>
                    <input type="file" name="logo" id="form-file">
                    @if($data['logo'])
                        <div class="image-form">
                            <img src="{{ resizeImage($data['logo'], 100, 100) }}"
                                 alt="{{ $data['websiteName'] }}"
                                 class="remodalImg"
                                 data-src="{{ asset($data['logo']) }}"
                            >
                        </div>
                    @endif
                </div>

            </section>

            <section id="contact">
                <div class="is-row">

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_firstname') }}</label>
                            <input type="text" name="firstname" value="{{ $data['firstname'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_lastname') }}</label>
                            <input type="text" name="lastname" value="{{ $data['lastname'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_company') }}</label>
                            <input type="text" name="company" value="{{ $data['company'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_siret') }}</label>
                            <input type="text" name="siret" value="{{ $data['siret'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_email') }}</label>
                            <input type="text" name="email" value="{{ $data['email'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_email_order') }}</label>
                            <input type="text" name="orderEmail" value="{{ $data['orderEmail'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_phone') }}</label>
                            <input type="text" name="phone" value="{{ $data['phone'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_address') }}</label>
                            <input type="text" name="address" value="{{ $data['address'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_zipCode') }}</label>
                            <input type="text" name="zipCode" value="{{ $data['zipCode'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_city') }}</label>
                            <input type="text" name="city" value="{{ $data['city'] }}">
                        </div>
                    </div>

                </div>
            </section>

            <section id="invoice">
                <div class="is-row">

                    <div class="is-col is-100">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_logo') }}</label>
                            <input type="file" name="invoiceLogo" id="form-file">
                            @if($data['invoiceLogo'])
                                <div class="image-form">
                                    <img src="{{ resizeImage($data['invoiceLogo'], 100, 100) }}"
                                         alt="{{ $data['websiteName'] }}"
                                         class="remodalImg"
                                         data-src="{{ asset($data['invoiceLogo']) }}"
                                    >
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_company') }}</label>
                            <input type="text" name="invoiceCompany" value="{{ $data['invoiceCompany'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_siret') }}</label>
                            <input type="text" name="invoiceSiret" value="{{ $data['invoiceTVA'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_tva') }}</label>
                            <input type="text" name="invoiceTVA" value="{{ $data['invoiceTVA'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_phone') }}</label>
                            <input type="text" name="invoicePhone" value="{{ $data['invoicePhone'] }}">
                        </div>
                    </div>

                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_note') }}</label>
                            <textarea cols="0" rows="0" name="invoiceNote">{{ $data['invoiceNote'] }}</textarea>
                            <div class="desc">{{ __('piclommerce::admin.setting_website_invoice_note_desc') }}</div>
                        </div>
                    </div>
                    <div class="is-col is-50">
                        <div class="form-item">
                            <label>{{ __('piclommerce::admin.setting_website_invoice_footer') }}</label>
                            <textarea cols="0" rows="0" name="invoiceFooter">{{ $data['invoiceFooter'] }}</textarea>
                            <div class="desc">{{ __('piclommerce::admin.setting_website_invoice_footer_desc') }}</div>
                        </div>
                    </div>

                </div>
            </section>

            <section id="socials">

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_website_facebook') }}</label>
                        <input type="text" name="facebook" value="{{ $data['facebook'] }}">
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_website_twitter') }}</label>
                        <input type="text" name="twitter" value="{{ $data['twitter'] }}">
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_website_pinterest') }}</label>
                        <input type="text" name="pinterest" value="{{ $data['pinterest'] }}">
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_website_googlePlus') }}</label>
                        <input type="text" name="googlePlus" value="{{ $data['googlePlus'] }}">
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_website_instagram') }}</label>
                        <input type="text" name="instagram" value="{{ $data['instagram'] }}">
                    </div>
                </div>

                <div class="is-col is-50">
                    <div class="form-item">
                        <label>{{ __('piclommerce::admin.setting_website_youtube') }}</label>
                        <input type="text" name="youtube" value="{{ $data['youtube'] }}">
                    </div>
                </div>

            </section>

            <section id="seo">

                <div class="form-item">
                    <label class="checkbox">
                        <input type="checkbox" name="seoRobot" value="1" {!! (!empty($data->seoRobot))?'checked="checked"':'' !!}>
                        {{ __('piclommerce::admin.setting_website_seo_robot') }}
                    </label>
                </div>

                <div class="form-item">
                    <label>{{ __('piclommerce::admin.setting_website_seo_analytics') }}</label>
                    <input type="text" name="analytics" value="{{ $data['analytics'] }}">
                </div>

                <div class="form-item">
                    <label>{{ __('piclommerce::admin.setting_website_seo_title') }}</label>
                    <input type="text" name="seoTitle" value="{{ $data['seoTitle'] }}">
                </div>

                <div class="form-item">
                    <label>{{ __('piclommerce::admin.setting_website_seo_description') }}</label>
                    <textarea cols="0" rows="0" name="seoDescription">{{ $data['seoDescription'] }}</textarea>
                </div>

            </section>

            <div class="form-item is-buttons">
                <button type="submit" class="button">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    {{ __('piclommerce::admin.save') }}
                </button>
            </div>
        </form>
    </div>

@endsection