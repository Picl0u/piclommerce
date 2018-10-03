@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-book"></i>
                    {{ __('piclommerce::admin.navigation_catalog') }} /
                    {{ __('piclommerce::admin.navigation_vat') }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_personalize') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_catalog') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_vat') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.shop.vats.create") }}">
                <i class="fa fa-plus"></i>
                {{ __("piclommerce::admin.add") }}
            </a>
            <div class="clear"></div>
        </div>

        <div class="datatable-container">
            <table class="datatable display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('piclommerce::admin.id') }}</th>
                    <th>{{ __('piclommerce::admin.shop_vat_name') }}</th>
                    <th>{{ __('piclommerce::admin.shop_vat_percent') }}</th>
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
                ajax : "{{ route('admin.shop.vats.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'percent', name: 'percent' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'actions', name: 'actions' }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 4
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