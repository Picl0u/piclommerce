@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-book"></i>
                    {{ __('piclommerce::admin.navigation_catalog') }} /
                    {{ __('piclommerce::admin.navigation_products') }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_catalog') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_products') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.shop.products.create") }}">
                <i class="fa fa-plus"></i>
                {{ __("piclommerce::admin.add") }}
            </a>
            <a href="{{ route("admin.shop.products.positions") }}">
                <i class="fa fa-list-ol"></i>
                {{ __("piclommerce::admin.position") }}
            </a>
            <a href="{{ route("admin.shop.products.imports") }}">
                <i class="fa fa-upload"></i>
                {{ __("piclommerce::admin.shop_product_import") }}
            </a>
            <a href="{{ route("admin.shop.products.export") }}">
                <i class="fa fa-download"></i>
                {{ __("piclommerce::admin.shop_product_export") }}
            </a>
            <a href="{{ route("admin.shop.products.attributes.imports") }}">
                <i class="fa fa-upload"></i>
                {{ __("piclommerce::admin.shop_product_import_attributes") }}
            </a>
            <a href="{{ route("admin.shop.products.export.attributes") }}">
                <i class="fa fa-download"></i>
                {{ __("piclommerce::admin.shop_product_export_attributes") }}
            </a>
            <div class="clear"></div>
        </div>

        <div class="datatable-container">
            <div class="buttons-datatable"></div>
            <table class="datatable display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('piclommerce::admin.id') }}</th>
                    <th>{{ __('piclommerce::admin.online') }}</th>
                    <th>{{ __('piclommerce::admin.medias') }}</th>
                    <th>{{ __('piclommerce::admin.shop_product_ref') }}</th>
                    <th>{{ __('piclommerce::admin.shop_product_name') }}</th>
                    <th>{{ __('piclommerce::admin.shop_product_price_ht') }}</th>
                    <th>{{ __('piclommerce::admin.shop_product_quantity') }}</th>
                    <th>{{ __('piclommerce::admin.last_update') }}</th>
                    <th>{{ __('piclommerce::admin.action') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        jQuery(function() {
            var table = jQuery('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax : "{{ route('admin.shop.products.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'published', name: 'published' },
                    { data: 'image', name: 'image' },
                    { data: 'reference', name: 'reference' },
                    { data: 'name', name: 'name' },
                    { data: 'price_ht', name: 'price_ht' },
                    { data: 'stock_available', name: 'stock_available' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'actions', name: 'actions' }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 8
                }],
                order: [[ 0, "desc" ]],
                stateSave: true,
                responsive: true,
                scrollX: true,
                language : {
                    url : 'http://cdn.datatables.net/plug-ins/1.10.16/i18n/French.json'
                },
            });
        });
    </script>
@endpush