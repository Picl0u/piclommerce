<h3>{{ $title }}</h3>
<form class="form-attributes" action="{{ $route }}" method="post">
    {{ csrf_field() }}
    <div class="attributes-container">
        @if(empty($data->declinaisons))
            <div class="is-row align-middle attribute" data-key="0">

                <div class="is-col is-40 form-item">
                    <label>Attribut</label>
                    <input type="text" name="attr[0]" value="">
                </div>
                <div class="is-col is-40 form-item">
                    <label>Valeur</label>
                    <input type="text" name="values[0]" value="">
                </div>
                <div class="is-col is-20 attribute-actions">
                    <span class="add-new-attribute">
                        <i class="fa fa-plus"></i>
                    </span>
                </div>
            </div>
        @else
            @php $declinaisons = $data->getValues('declinaisons'); @endphp
            @foreach($declinaisons as $key => $value)
                <div class="is-row align-middle attribute" data-key="{{ $loop->index }}">

                    <div class="is-col is-40 form-item">
                        <label>Attribut</label>
                        <input type="text" name="attr[{{ $loop->index }}]" value="{{ $key }}">
                    </div>
                    <div class="is-col is-40 form-item">
                        <label>Valeur</label>
                        <input type="text" name="values[{{ $loop->index }}]" value="{{ $value }}">
                    </div>
                    <div class="is-col is-20 attribute-actions">
                        <span class="add-new-attribute">
                            <i class="fa fa-plus"></i>
                        </span>
                        @if($loop->index > 0)
                            <span class="delete-attribute">
                                <i class="fa fa-trash"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="form-item">
        <label>Quantité</label>
        <input type="text" name="stock_brut" value="{{ $data->stock_brut }}">
    </div>
    <div class="form-item">
        <label>Impact sur le prix</label>
        <div class="is-append">
            <input type="text" name="price_impact" value="{{ $data->price_impact }}">
            <span>{{ config("piclommerce.currency") }}</span>
        </div>
    </div>

    <div class="is-row">
        <div class="is-col">
            <div class="form-item">
                <label>Référence</label>
                <input type="text" name="reference" value="{{ $data->reference }}">
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label>Code-barre EAN-13 ou JAN</label>
                <input type="text" name="ean_code" value="{{ $data->ean_code }}">
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label>Code-barre UPC</label>
                <input type="text" name="upc_code" value="{{ $data->upc_code }}">
            </div>
        </div>
        <div class="is-col">
            <div class="form-item">
                <label>Code-barre ISBN</label>
                <input type="text" name="isbn_code" value="{{ $data->isbn_code }}">
            </div>
        </div>
    </div>

    <div class="image-choose-attributes">
        <label>Image(s) associée(s)</label>
        <div class="is-row">
            @foreach($medias as $image)
                @php $selected = false; @endphp
                <div class="is-col is-image">
                    @if(isset($images) && !empty($images) && is_array($images))
                        @foreach($images as $img)
                            @php
                                if($img == $image['uuid']) {
                                    $selected= true;
                                }
                            @endphp
                        @endforeach
                    @endif
                    <img src="{{ resizeImage($image['target_path'], 100 ,100) }}" alt="{{ $image['alt'] }}" {!! ($selected)?'class="is-selected"':'' !!} >
                    <input type="checkbox" name="images[]" value="{{ $image['uuid'] }}" {!! ($selected)?'checked="checked"':'' !!} >
                </div>
            @endforeach
        </div>
    </div>

    <button data-remodal-action="confirm" class="remodal-confirm">
        {{ __('piclommerce::admin.save') }}
    </button>
</form>