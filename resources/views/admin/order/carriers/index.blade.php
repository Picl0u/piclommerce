@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-shopping-cart"></i>
                    {{ __('piclommerce::admin.navigation_orders') }} / {{ __("piclommerce::admin.navigation_carriers") }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_carriers') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.order.carriers.create") }}">
                <i class="fa fa-plus"></i>
                {{ __("piclommerce::admin.add") }}
            </a>
            <div class="clear"></div>
        </div>
        @if(empty($countDefault))
            <div class="alert is-focus">
                <h6>{{ __("piclommerce::order_carriers_warning") }}</h6>
                <p>{{ __("piclommerce::order_carriers_default_error") }}</p>
            </div>
        @else
            @if($countDefault > 1)
                <div class="alert is-focus">
                    <h6>{{ __("piclommerce::order_carriers_warning") }}</h6>
                    <p>
                        {{ __("piclommerce::order_carriers_default_error") }}<br>
                        {{ $countDefault }} {{ __("piclommerce::order_carriers_default_count_error") }}
                    </p>
                </div>
            @endif
        @endif

        <div class="datatable-container">
            <table class="datatable display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('piclommerce::admin.id') }}</th>
                    <th>{{ __('piclommerce::admin.online') }}</th>
                    <th>{{ __('piclommerce::admin.order_carriers_default') }}</th>
                    <th>{{ __('piclommerce::admin.medias') }}</th>
                    <th>{{ __('piclommerce::admin.order_carriers_name') }}</th>
                    <th>{{ __('piclommerce::admin.order_carriers_delay') }}</th>
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
                ajax : "{{ route('admin.order.carriers.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'published', name: 'published' },
                    { data: 'default', name: 'default' },
                    { data: 'image', name: 'image' },
                    { data: 'name', name: 'name' },
                    { data: 'delay', name: 'delay' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'actions', name: 'actions' }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 7
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