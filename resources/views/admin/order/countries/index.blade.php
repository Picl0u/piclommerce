@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-shopping-cart"></i>
                    {{ __('piclommerce::admin.navigation_orders') }} / {{ __("piclommerce::admin.navigation_countries") }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_countries') }}</span>
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
                    <th>{{ __('piclommerce::admin.order_countries_activate') }}?</th>
                    <th>{{ __('piclommerce::admin.order_countries_name') }}</th>
                    <th>{{ __('piclommerce::admin.order_countries_iso') }}</th>
                    <th>{{ __('piclommerce::admin.order_countries_currency') }}</th>
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
                ajax : "{{ route('admin.order.countries.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'activated', name: 'activated' },
                    { data: 'name', name: 'name' },
                    { data: 'iso_3166_2', name: 'iso_3166_2' },
                    { data: 'currency_symbol', name: 'currency_symbol' },
                    { data: 'actions', name: 'actions' },
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 5
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