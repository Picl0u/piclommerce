@extends("piclommerce::layouts.admin")

@section("content")
    <div class="page-title">
        <div class="is-row align-middle">
            <div class="is-col">
                <h1>
                    <i class="fa fa-shopping-cart"></i>
                    {{ __('piclommerce::admin.navigation_orders') }} / {{ __("piclommerce::admin.navigation_invoices") }}
                </h1>
            </div>
            <div class="is-col text-right">
                <nav class="breadcrumb">
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_sale') }}</a>
                    <a href="{{ route("admin.dashboard") }}">{{ __('piclommerce::admin.navigation_orders') }}</a>
                    <span>{{ __('piclommerce::admin.navigation_invoices') }}</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="content-container">

        <form method="post" action="{{ route("admin.orders.orders.invoices.export") }}">
            {{ csrf_field() }}
            <div class="form-item">
                <label>{{ __("piclommerce::admin.order_du") }}</label>
                <input type="text" name="date_begin" class="date-picker" value="">
            </div>
            <div class="form-item">
                <label>{{ __("piclommerce::admin.order_au") }}</label>
                <input type="text" name="date_end" class="date-picker" value="">
            </div>

            <div class="form-item is-buttons">
                <button type="submit" class="button">
                    {{ __('piclommerce::admin.order_export') }}
                </button>
            </div>

        </form>

        <div class="invoice-list">
            <ul>
                @foreach($exports as $export)
                    <li>
                        Export du {{ Carbon\Carbon::parse($export->begin)->format('d/m/Y') }}
                        au {{ Carbon\Carbon::parse($export->end)->format('d/m/Y') }} -
                        Généré le : {{ $export->created_at->format('d/m/Y à H:i') }} -

                        <a href="{{ route("admin.orders.orders.invoices.download",['uuid' => $export->uuid]) }}" class="label focus">
                            Télécharger les factures
                        </a>

                    </li>
                @endforeach
            </ul>
        </div>
    </div>

@endsection