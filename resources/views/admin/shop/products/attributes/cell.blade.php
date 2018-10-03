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
                                'id' => $attribute->product_id,
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