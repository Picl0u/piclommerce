@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-book"></i>
                    {{ __('piclommerce::admin.navigation_catalog') }} /
                    {{ __('piclommerce::admin.navigation_products') }}
                    <span>{{ __('piclommerce::admin.import') }}</span>
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_personalize') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_catalog') }}</a>
                    <a href="{{ route('admin.shop.products.index') }}">{{ __('piclommerce::admin.navigation_products') }}</a>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="button-actions">
            <a href="{{ route("admin.shop.products.index") }}">
                <i class="fa fa-arrow-left"></i>
                {{ __('piclommerce::admin.return') }}
            </a>
            <div class="clear"></div>
        </div>

        <form class="admin-form" method="post" action="{{ route('admin.shop.products.imports.attributes.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-item">
                <label for="form-file">
                    {{ __('piclommerce::admin.shop_product_import_file') }}
                </label>
                <input type="file" name="file" id="form-file">
                <div class="desc">{{ __('piclommerce::admin.shop_product_import_file_desc') }}</div>
            </div>
            <div class="form-item is-buttons">
                <button type="submit" class="button">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    {{ __('piclommerce::admin.save') }}
                </button>
            </div>
        </form>
        <hr>
        <h3>{{ __('piclommerce::admin.shop_product_import_last') }}</h3>
        <div class="file-list">
            @if($files)
                <ul>
                    @foreach($files as $file)
                        <li>
                            {{ __('piclommerce::admin.shop_product_import_name') }} <strong>{{ $file->getFilename () }}</strong> -
                            {{ __('piclommerce::admin.shop_product_import_date') }} <strong>{{ date ("d/m/Y à H:i:s.", filemtime($file->getPathname())) }}</strong>
                            <a class="label focus" href="{{ asset($file->getPathname()) }}" target="_blank">
                                {{ __('piclommerce::admin.shop_product_import_download') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>{{ __('piclommerce::admin.shop_product_import_empty') }}</p>
            @endif
        </div>
    </div>

@endsection