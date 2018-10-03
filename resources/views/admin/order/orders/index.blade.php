@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-shopping-cart"></i>
                    {{ __('piclommerce::admin.navigation_orders') }} / {{ __("piclommerce::admin.navigation_orders") }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_orders') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">

        <div class="datatable-container">
            <table class="datatable display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('piclommerce::admin.id') }}</th>
                    <th>{{ __('piclommerce::admin.orders_reference') }}</th>
                    <th>{{ __('piclommerce::admin.orders_user_register') }}</th>
                    <th>{{ __('piclommerce::admin.orders_user') }}</th>
                    <th>{{ __('piclommerce::admin.orders_delivery') }}</th>
                    <th>{{ __('piclommerce::admin.orders_total') }}</th>
                    <th>{{ __('piclommerce::admin.orders_status') }}</th>
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
            jQuery('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax : "{{ route('admin.orders.orders.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'reference', name: 'reference' },
                    { data: 'user_id', name: 'user_id' },
                    { data: 'user_firstname', name: 'user_firstname' },
                    { data: 'delivery_country_name', name: 'delivery_country_name' },
                    { data: 'price_ttc', name: 'price_ttc' },
                    { data: 'status_id', name: 'status_id' },
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