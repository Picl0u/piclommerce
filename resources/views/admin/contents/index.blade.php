@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-files-o"></i>
                    {{ __('piclommerce::admin.navigation_pages') }} /
                    {{ __('piclommerce::admin.navigation_contents') }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_personalize') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_pages') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_contents') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.pages.contents.create") }}">
                <i class="fa fa-plus"></i>
                {{ __("piclommerce::admin.add") }}
            </a>
            <a href="{{ route("admin.pages.contents.positions") }}">
                <i class="fa fa-list-ol"></i>
                {{ __("piclommerce::admin.position") }}
            </a>
            <div class="clear"></div>
        </div>

        <div class="datatable-container">
            <table class="datatable display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('piclommerce::admin.id') }}</th>
                    <th>{{ __('piclommerce::admin.online') }}</th>
                    <th>{{ __('piclommerce::admin.medias') }}</th>
                    <th>{{ __('piclommerce::admin.content_pages_name') }}</th>
                    <th>{{ __('piclommerce::admin.content_categories_name') }}</th>
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
                ajax : "{{ route('admin.pages.contents.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'published', name: 'published' },
                    { data: 'image', name: 'image' },
                    { data: 'name', name: 'name' },
                    { data: 'content_category_id', name: 'content_category_id' },
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