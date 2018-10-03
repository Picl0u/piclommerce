@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-envelope-o"></i>
                    {{ __('piclommerce::admin.navigation_newsletter') }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_personalize') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_newsletter') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.newsletter.create") }}">
                <i class="fa fa-plus"></i>
                {{ __("piclommerce::admin.add") }}
            </a>
            <a href="{{ route("admin.newsletter.export") }}">
                <i class="fa fa-download"></i>
                {{ __("piclommerce::admin.export") }}
            </a>
            <div class="clear"></div>
        </div>

        <div class="datatable-container">
            <table class="datatable display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('piclommerce::admin.id') }}</th>
                    <th>{{ __('piclommerce::admin.online') }}</th>
                    <th>{{ __('piclommerce::admin.user_email') }}</th>
                    <th>{{ __('piclommerce::admin.user_firstname') }}</th>
                    <th>{{ __('piclommerce::admin.user_lastname') }}</th>
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
                ajax : "{{ route('admin.newsletter.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'active', name: 'active' },
                    { data: 'email', name: 'email' },
                    { data: 'firstname', name: 'firstname' },
                    { data: 'lastname', name: 'lastname' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'actions', name: 'actions' }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 6
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