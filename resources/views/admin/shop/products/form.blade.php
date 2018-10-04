<div class="is-row">
    <div class="is-col is-75">
        <nav class="tabs" data-kube="tabs" data-equal="true">
            <a href="#infos" class="is-active">{{ __('piclommerce::admin.informations') }}</a>
            <a href="#prices">{{ __('piclommerce::admin.shop_product_price') }}</a>
            <a href="#transport">{{ __('piclommerce::admin.shop_product_delivery') }}</a>
            <a href="#attributes">{{ __('piclommerce::admin.shop_product_attributes') }}</a>
            <a href="#medias">{{ __('piclommerce::admin.medias') }}</a>
            <a href="#seo">{{ __('piclommerce::admin.seo') }}</a>
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
                            <input type="checkbox" name="week_selection" value="1" {!! (!empty($data->week_selection))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.shop_product_week_selection') }} ?
                        </label>
                    </div>
                </div>
            </div>
            <div class="is-row">
                <div class="is-col">
                    <div class="form-item">
                        <label for="form-name">{{ __('piclommerce::admin.shop_product_name') }}</label>
                        <input type="text" name="name" id="form-name" value="{{ $data->name }}">
                    </div>
                </div>
            </div>

            <div class="is-row">
                <div class="is-col">
                    <div class="form-item">
                        <label for="form-reference">{{ __('piclommerce::admin.shop_product_ref') }}</label>
                        <input type="text" name="reference" id="form-reference" value="{{ $data->reference }}">
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label for="form-ean_code">{{ __('piclommerce::admin.shop_product_ean_code') }}</label>
                        <input type="text" name="ean_code" id="form-ean_code" value="{{ $data->ean_code }}">
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label for="form-upc_code">{{ __('piclommerce::admin.shop_product_upc_code') }}</label>
                        <input type="text" name="upc_code" id="form-upc_code" value="{{ $data->upc_code }}">
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label for="form-isbn_code">{{ __('piclommerce::admin.shop_product_isbn_code') }}</label>
                        <input type="text" name="isbn_code" id="form-isbn_code" value="{{ $data->isbn_code }}">
                    </div>
                </div>
            </div>

            <div class="form-item">
                <label for="form-quantity">{{ __('piclommerce::admin.shop_product_quantity') }}</label>
                <input type="text" name="stock_brut" id="form-quantity" value="{{ $data->stock_brut }}">
            </div>

            <div class="form-item">
                <label for="form-shop_category_id">{{ __('piclommerce::admin.shop_product_category') }}</label>
                {!! nestableExtends($categories_array)->attr(['name'=>'shop_category_id'])->selected($data->shop_category_id)->renderAsDropdown() !!}
            </div>

            <div class="form-item">
                <label for="form-categories">{{ __('piclommerce::admin.shop_product_categories') }}</label>
                <div class="checkboxes-tree">
                    <?php
                    $tree = new \Piclou\Piclommerce\Helpers\TreeCheckboxes('categories','shop_category_id', $data->ProductsHasCategories);
                    foreach($categories as $category) {
                        $tree->addRow($category['id'], (empty($category['parent_id']))?null:$category['parent_id'], $category['name']);
                    }
                    echo $tree->generateList();
                    ?>
                </div>
            </div>

            <div class="form-item">
                <label for="form-associates">{{ __('piclommerce::admin.shop_product_associates') }}</label>
                <select id="form-associates" name="associates[]" multiple="multiple" class="multiple-select">
                    @foreach($products as $product)
                        <?php
                        $selected="";
                        foreach ($data->ProductsAssociates as $p) {
                            if($p->product_id == $product->id){
                                $selected='selected="selected"';
                            }
                        }
                        ?>
                        <option value="{{ $product->id }}" {{ $selected }}>
                            {{ $product->name }} - ref : {{ $product->reference }}
                        </option>
                    @endforeach
                </select>
                <div class="desc">{{ __('piclommerce::admin.shop_product_associates_desc') }}</div>
            </div>

            <div class="form-item">
                <label for="form-summary">{{ __('piclommerce::admin.shop_product_summary') }}</label>
                <textarea class="html-editor" cols="0" rows="0" name="summary" id="form-summary">{{ $data->summary }}</textarea>
            </div>

            <div class="form-item">
                <label for="form-description">{{ __('piclommerce::admin.shop_product_description') }}</label>
                <textarea class="html-editor" cols="0" rows="0" name="description" id="form-description">{{ $data->description }}</textarea>
            </div>

        </section>

        <section id="prices">

            <div class="form-item">
                <label for="form-price_ht">{{ __('piclommerce::admin.shop_product_price_ht') }}</label>
                <div class="is-append">
                    <input type="text" name="price_ht" id="form-price_ht" value="{{ $data->price_ht }}">
                    <span>{{ config("piclommerce.currency") }}</span>
                </div>
            </div>

            <div class="form-item">
                <label for="form-vat">{{ __('piclommerce::admin.shop_product_vat') }}</label>
                <select id="form-vat" name="vat_id">
                    @foreach($vats as $vat)
                        <option value="{{ $vat->id }}" data-taux="{{$vat->percent}}" {!! ($data->vat_id == $vat->id)?'selected="selected"':'' !!}>
                            {{ $vat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-item">
                <label for="form-price_ttc">{{ __('piclommerce::admin.shop_product_price_ttc') }}</label>
                <div class="is-append">
                    <input type="text" name="price_ttc" id="form-price_ttc" value="{{ $data->price_ttc }}">
                    <span>{{ config("piclommerce.currency") }}</span>
                </div>
            </div>

            <div class="form-item">
                <label for="form-reduce_date_begin">{{ __('piclommerce::admin.shop_product_reduce_date_begin') }}</label>
                <input type="text" name="reduce_date_begin" class="datetime-picker" id="form-reduce_date_begin" data-default-date='{{ \Carbon\Carbon::parse($data->reduce_date_begin) }}' value="{{ \Carbon\Carbon::parse($data->reduce_date_begin) }}">
                <div class="desc">{{ __('piclommerce::admin.shop_product_reduce_date_begin_desc') }}</div>
            </div>

            <div class="form-item">
                <label for="form-reduce_date_end">{{ __('piclommerce::admin.shop_product_reduce_date_end') }}</label>
                <input type="text" name="reduce_date_end" class="datetime-picker" id="form-reduce_date_end" data-default-date='{{ \Carbon\Carbon::parse($data->reduce_date_end) }}'  value="{{ \Carbon\Carbon::parse($data->reduce_date_end) }}">
                <div class="desc">{{ __('piclommerce::admin.shop_product_reduce_date_end_desc') }}</div>
            </div>

            <div class="form-item">
                <label for="form-reduce_price">{{ __('piclommerce::admin.shop_product_reduce_price') }}</label>
                <div class="is-append">
                    <input type="text" name="reduce_price" id="form-reduce_price" value="{{ $data->reduce_price }}">
                    <span>{{ config("piclommerce.currency") }}</span>
                </div>
            </div>

            <div class="form-item">
                <label for="form-reduce_percent">{{ __('piclommerce::admin.shop_product_reduce_percent') }}</label>
                <div class="is-append">
                    <input type="text" name="reduce_percent" id="form-reduce_percent" value="{{ $data->reduce_percent }}">
                    <span>%</span>
                </div>
            </div>

        </section>

        <section id="transport">

            <div class="form-item">
                <label for="form-weight">{{ __('piclommerce::admin.shop_product_weight') }}</label>
                <input type="text" name="weight" id="form-weight" value="{{ $data->weight }}">
            </div>

            <div class="form-item">
                <label for="form-height">{{ __('piclommerce::admin.shop_product_height') }}</label>
                <input type="text" name="height" id="form-height" value="{{ $data->height }}">
            </div>

            <div class="form-item">
                <label for="form-length">{{ __('piclommerce::admin.shop_product_length') }}</label>
                <input type="text" name="length" id="form-length" value="{{ $data->length }}">
            </div>

            <div class="form-item">
                <label for="form-width">{{ __('piclommerce::admin.shop_product_width') }}</label>
                <input type="text" name="width" id="form-width" value="{{ $data->width }}">
            </div>

        </section>

        <section id="attributes">
            <div class="declinaisons-actions">
                @if(!is_null($data->id))
                    <a href="{{ route('admin.shop.products.attribute.add', ['id' => $data->id]) }}"
                       class="add-new-declinaison button"
                    >
                        <i class="fa fa-plus"></i>
                        {{ __('piclommerce::admin.add') }}
                    </a>
                @else
                    <p>
                        <i class="fa fa-exclamation-triangle"></i>
                        {{ __("piclommerce::admin.shop_product_declinaison_not_id") }}
                    </p>
                @endif
            </div>
            <table class="bordered striped table-declinaisons">
                <thead>
                <tr>
                    <th class="w20">Déclinaisons</th>
                    <th class="w30">Référence</th>
                    <th class="w20">Quantité</th>
                    <th class="w30">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data->ProductsAttributes as $attribute)
                    <tr>
                        <td>
                            @php $declinaisons = $attribute->getValues('declinaisons'); @endphp
                            @foreach($declinaisons as $key => $value)
                                <span class="declinaison-value"><strong>{{ $key }} :</strong> {{ $value }}</span>
                            @endforeach
                        </td>
                        <td>
                            {{ $attribute->reference }}
                        </td>
                        <td>
                            {{ $attribute->stock_brut }}
                        </td>
                        <td class="table-actions">
                            <a href="{{ route('admin.shop.products.attribute.edit',[
                                'id' => $data->id,
                                'uuid' => $attribute->uuid
                            ]) }}"
                               class="edit-declinaison">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="{{ route('admin.shop.products.attribute.delete',['uuid' => $attribute->uuid]) }}"
                               class="delete-declinaison">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <div class="remodal" data-remodal-id="modal-attributes" data-remodal-options="hashTracking:false">
            <span data-remodal-action="close" class="remodal-close"></span>
            <div class="modal-container"></div>
        </div>

        <section id="medias">
            <div class="is-row">
                <div class="is-col is-75">
                    <h5>{{ __('piclommerce::admin.shop_product_vignette') }}</h5>
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
                                    <a href="{{ route('admin.shop.products.image.update',['uuid' => $data['uuid']]) }}"
                                       class="table-button edit-media"
                                    >
                                        <i class="fa fa-floppy-o"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="is-col is-25">
                    <label for="form-file">
                        {{ __('piclommerce::admin.medias_single_upload') }}
                    </label>
                    <input type="file" name="image" id="form-file">
                </div>
            </div>

            <div class="is-row">
                <div class="is-col is-75">
                    <h5>{{ __('piclommerce::admin.shop_product_images') }}</h5>
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
                        @if(!$data->imageList)
                            <tr>
                                <td colspan="5">{{ __('piclommerce::admin.no_data') }}</td>
                            </tr>
                        @else
                            @foreach($data->getMedias("imageList") as $medias)
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
                                        <a href="{{ route('admin.shop.products.imagelist.update',['uuid' => $data['uuid'], "image" => $medias['uuid']]) }}"
                                           class="table-button edit-media"
                                        >
                                            <i class="fa fa-floppy-o"></i>
                                        </a>
                                        <a href="{{ route('admin.shop.products.imagelist.delete',['uuid' => $data['uuid'], "image" => $medias['uuid']]) }}"
                                           class="table-button delete-button confirm-alert"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="is-col is-25">
                    <label for="form-imageList">
                        {{ __('piclommerce::admin.medias_multiple_upload') }}
                    </label>
                    <input type="file" multiple="multiple" name="imageList[]" id="form-imageList">
                </div>
            </div>

        </section>

        <section id="seo">
            @include("piclommerce::components.admin.seo.seo_form")
        </section>

    </div>

    <div class="is-col is-25">
        @include("piclommerce::components.admin.seo.seo_optimisation")
    </div>
</div>