<div class="plage-list">
    @if(count($data->CarriersPrices) > 0)
        <?php
            $carriersPrices = $data->CarriersPrices;
            $prices = \Piclou\Piclommerce\Http\Entities\CarriersPrices::where('carriers_id', $data->id)->groupBy('key')->get();
        ?>
       @foreach($prices as $price)
        <div class="col-plage" data-key="{{ $price->key }}">
            <div class="line-plage head-plage">
                Sera appliquée lorsque le <span class="type-shipping">prix</span> est &gt;=
                <div class="is-prepend">
                    <span>{{ config("piclommerce.currency") }}</span>
                    <input type="text"
                           name="priceMin[{{ $price->key }}]"
                           placeholder="0.00"
                           class="price-min"
                           value="{{ $price->price_min }}"
                    >
                </div>
            </div>
            <div class="line-plage head-plage">
                Sera appliquée lorsque le <span class="type-shipping">prix</span> &lt;
                <div class="is-prepend">
                    <span>{{ config("piclommerce.currency") }}</span>
                    <?php
                        $value = $price->price_max;
                        if(empty($value)) {
                            $value = 'Illimité';
                        }
                    ?>
                    <input type="text"
                           name="priceMax[{{ $price->key }}]"
                           placeholder="0.00"
                           class="price-max"
                           value="{{ $value }}"
                    >
                </div>
            </div>
            <div class="line-plage">
                <label class="checkbox">
                    <input type="checkbox" name="availableAll[{{ $price->key }}]" value="1" class="check-all">
                    Tous les pays
                </label>
                <div class="is-prepend">
                    <span>{{ config("piclommerce.currency") }}</span>
                    <input type="text" name="allCountries[{{ $price->key }}]" placeholder="0.00" value="" class="value-all">
                </div>
            </div>
            @foreach($countries as $country)
                <div class="line-plage country-plage">
                    <label class="checkbox">
                        <?php
                        $checked = "";
                        foreach($carriersPrices as $c) {
                            if($c->key == $price->key && $c->country_id == $country->id) {
                                $checked = 'checked';
                            }
                        }
                        ?>
                        <input type="checkbox" name="availableCountry[{{ $price->key }}][{{ $country->id }}]" value="1" {{$checked}}>
                        {{ $country->name }}
                    </label>
                    <div class="is-prepend">
                        <span>{{ config("piclommerce.currency") }}</span>
                        <?php
                            $value = "";
                            foreach($carriersPrices as $c) {
                                if($c->key == $price->key && $c->country_id == $country->id) {
                                    $value = $c->price;
                                }
                            }
                        ?>
                        <input type="text"
                               name="countries[{{ $price->key }}][{{ $country->id }}]"
                               placeholder="0.00"
                               value="{{ $value }}"
                        >
                    </div>
                </div>
            @endforeach
            @if($price->key > 0)
                <a href="#" class="delete-plage">Supprimer</a>
            @endif
        </div>
        @endforeach
   @else
        <div class="col-plage" data-key="0">
            <div class="line-plage head-plage">
                Sera appliquée lorsque le <span class="type-shipping">prix</span> est &gt;=
                <div class="is-prepend">
                    <span>{{ config("piclommerce.currency") }}</span>
                    <input type="text" name="priceMin[0]" placeholder="0.00" class="price-min" value="">
                </div>
            </div>
            <div class="line-plage head-plage">
                Sera appliquée lorsque le <span class="type-shipping">prix</span> &lt;
                <div class="is-prepend">
                    <span>{{ config("piclommerce.currency") }}</span>
                    <input type="text" name="priceMax[0]" placeholder="0.00" class="price-max" value="">
                </div>
            </div>
            <div class="line-plage">
                <label class="checkbox">
                    <input type="checkbox" name="availableAll[0]" value="1" class="check-all">
                    Tous les pays
                </label>
                <div class="is-prepend">
                    <span>{{ config("piclommerce.currency") }}</span>
                    <input type="text" name="allCountries[0]" placeholder="0.00" value="" class="value-all">
                </div>
            </div>
            @foreach($countries as $country)
                <div class="line-plage country-plage">
                    <label class="checkbox">
                        <input type="checkbox" name="availableCountry[0][{{ $country->id }}]" value="1">
                        {{ $country->name }}
                    </label>
                    <div class="is-prepend">
                        <span>{{ config("piclommerce.currency") }}</span>
                        <input type="text"
                               name="countries[0][{{ $country->id }}]"
                               placeholder="0.00"
                               value=""
                        >
                    </div>
                </div>
            @endforeach
        </div>
   @endif
</div>
<div class="clear"></div>
<a href="#" class="add-new-plage">
    <i class="fa fa-plus"></i>
    Ajouter une nouvelle tranche
</a>