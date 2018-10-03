<nav class="tabs" data-kube="tabs" data-equal="true">
    <a href="#infos" class="is-active">{{ __('piclommerce::admin.informations') }}</a>
    <a href="#prices">{{ __('piclommerce::admin.order_carriers_destination_prices') }}</a>
    <a href="#medias">{{ __('piclommerce::admin.medias') }}</a>
</nav>

<section id="infos">
    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label class="checkbox">
                    <input type="checkbox" name="published" value="1" {!! (!empty($data->published))?'checked="checked"':'' !!}>
                    {{ __('piclommerce::admin.online') }} ?
                </label>
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label class="checkbox">
                    <input type="checkbox" name="default" value="1" {!! (!empty($data->default))?'checked="checked"':'' !!}>
                    {{ __('piclommerce::admin.order_carriers_default') }} ?
                </label>
            </div>
        </div>
    </div>
    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label for="form-name">{{ __('piclommerce::admin.order_carriers_name') }} ?</label>
                <input type="text" name="name" id="form-name" value="{{ $data->name }}">
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label for="form-color">{{ __('piclommerce::admin.order_carriers_default_price') }}</label>
                <input type="text" name="default_price" value="{{ $data->default_price }}">
            </div>
        </div>
    </div>

    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label for="form-delay">{{ __('piclommerce::admin.order_carriers_delay') }}</label>
                <input type="text" name="delay" value="{{ $data->delay }}">
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label for="form-url">{{ __('piclommerce::admin.order_carriers_url') }}</label>
                <input type="text" name="url" value="{{ $data->url }}">
                <div class="desc">{{ __('piclommerce::admin.order_carriers_url_desc') }}</div>
            </div>
        </div>
    </div>
</section>

<section id="prices">
    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label class="checkbox">
                    <input type="checkbox" name="free" value="1" {!! (!empty($data->free))?'checked="checked"':'' !!}>
                    {{ __('piclommerce::admin.order_carriers_free') }} ?
                </label>
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label class="checkbox">
                    <input type="radio" name="type_shipping" value="weight" {!! (!empty($data->weight))?'checked="checked"':'' !!}>
                    {{ __('piclommerce::admin.order_carriers_weight_shipping') }} ?
                </label>
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label class="checkbox">
                    <input type="radio" name="type_shipping" value="price" {!! (!empty($data->price) || empty($data->weight))?'checked="checked"':'' !!}>
                    {{ __('piclommerce::admin.order_carriers_price_shipping') }} ?
                </label>
            </div>
        </div>
    </div>
    <label>{{ __("piclommerce::admin.order_carriers_plages") }}</label>
    <div class="carriers-plages">
        @include('piclommerce::admin.order.carriers.plages.price', compact('data','countries'))
    </div>
</section>

<section id="medias">
    <div class="is-row">
        <div class="is-col is-75">
            <table class="is-bordered is-striped is-responsive">
                <thead>
                <tr>
                    <th>
                        {{ __('piclommerce::admin.medias_image') }}
                    </th>
                    <th>
                        {{ __('piclommerce::admin.medias_title') }}
                    </th>
                    <th>
                        {{ __('piclommerce::admin.medias_description') }}
                    </th>
                    <th>
                        {{ __('piclommerce::admin.medias_type') }}
                    </th>
                    <th>

                    </th>
                </tr>
                </thead>
                <tbody>
                @if(!$data->image)
                    <tr>
                        <td colspan="5">{{ __('piclommerce::admin.no_data') }}</td>
                    </tr>
                @else
                    @php $medias = $data->getMedias("image"); @endphp
                    <tr>
                        <td data-label="{{ __('piclommerce::admin.medias_image') }}">
                            <img src="{{ resizeImage($medias['target_path'], 30 ,30) }}"
                                 alt="{{ $medias['alt'] }}"
                                 class="remodalImg"
                                 data-src="/{{ $medias['target_path'] }}"
                            >
                        </td>
                        <td data-label="{{ __('piclommerce::admin.medias_title') }}">
                            <input type="text" name="medias_alt" value="{{ $medias['alt'] }}">
                        </td>
                        <td data-label="{{ __('piclommerce::admin.medias_description') }}">
                            <input type="text" name="medias_description" value="{{ $medias['description'] }}">
                        </td>
                        <td data-label="{{ __('piclommerce::admin.medias_type') }}">
                            {{ $medias['file_type'] }}
                        </td>
                        <td>
                            <a href="{{ route('admin.sliders.image.update',['uuid' => $data['uuid']]) }}"
                               class="table-button edit-media"
                            >
                                <i class="fa fa-floppy-o"></i> {{ __("piclommerce::admin.edit") }}
                            </a>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="is-col is-25">
            <label for="form-file">
                {{ __('piclommerce::admin.order_carriers_logo') }}</label>
            <input type="file" name="image" id="form-file">
        </div>
    </div>

</section>