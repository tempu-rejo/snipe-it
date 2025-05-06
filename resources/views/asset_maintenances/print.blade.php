<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('admin/asset_maintenances/general.maintenance') }} - {{ date('Y-m-d H:i', time()) }}</title>
    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">
    <link rel="stylesheet" href="{{ url(mix('css/dist/bootstrap-table.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">
    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": 50
            }
        };
    </script>
    <style>
        body {
            font-family: "Arial, Helvetica", sans-serif;
            padding: 20px;
        }
        table.inventory {
            width: 100%;
            border: 1px solidrgb(0, 2, 8);
        }
        @page {
            size: A4;
        }
        .print-logo {
            max-height: 40px;
        }
        h4 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
    </style>
</head>


<body>
@if ($snipeSettings->logo_print_assets=='1')
    @if ($snipeSettings->brand == '3')
        <h2>
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}">
            @endif
            {{ $snipeSettings->site_name }}
        </h2>
    @elseif ($snipeSettings->brand == '2')
        @if ($snipeSettings->logo!='')
            <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}">
        @endif
    @else
        <h2>{{ $snipeSettings->site_name }}</h2>
    @endif
@endif


<h3>
    <p>S U R A T   J A L A N</p>
    {{ trans('admin/asset_maintenances/general.maintenance') }}: {{ $assetMaintenance->title ?? '' }}
</h3>
<p>{{ trans('admin/users/general.all_assigned_list_generation')}} {{ Helper::getFormattedDateObject(now(), 'datetime', false) }}</p>
<table class="snipe-table table table-striped inventory" id="AssetsMaintenance">
    <tbody>
        <tr>
            <!-- <th>{{ trans('admin/asset_maintenances/form.asset') }}</th> -->
            <th>{{ trans('general.asset') }}</th>
            <td>{{ optional($assetMaintenance->asset)->present()->fullName ?? '-' }}</td>
        </tr>
        <tr>
            <th>{{ trans('admin/asset_maintenances/form.asset_maintenance_type') }}</th>
            <td>{{ $assetMaintenance->asset_maintenance_type ?? '-' }}</td>
        </tr>
        <tr>
            <th>{{ trans('general.supplier') }}</th>
            <td>{{ optional($assetMaintenance->supplier)->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>{{ trans('admin/asset_maintenances/form.start_date') }}</th>
            <td>{{ Helper::getFormattedDateObject($assetMaintenance->start_date, 'date', false) }}</td>
        </tr>
        <tr>
            <th>{{ trans('admin/asset_maintenances/form.completion_date') }}</th>
            <td>
                @if ($assetMaintenance->completion_date)
                    {{ Helper::getFormattedDateObject($assetMaintenance->completion_date, 'date', false) }}
                @else
                    {{ trans('admin/asset_maintenances/message.asset_maintenance_incomplete') }}
                @endif
            </td>
        </tr>
        <tr>
            <th>{{ trans('admin/asset_maintenances/form.cost') }}</th>
            <td>{{ $assetMaintenance->cost ? Helper::formatCurrencyOutput($assetMaintenance->cost) : '-' }}</td>
        </tr>
        <tr>
            <th>{{ trans('admin/asset_maintenances/form.notes') }}</th>
            <td>{!! $assetMaintenance->notes ? nl2br(e($assetMaintenance->notes)) : '-' !!}</td>
        </tr>
    </tbody>
</table>
<table style="margin-top: 80px;">
    <tr>
        <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">{{ trans('general.signed_off_by') }}:</td>
        <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
        <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
        <td>_____________</td>
    </tr>
    <tr style="height: 80px;">
        <td></td>
        <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }}</td>
        <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
        <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
    </tr>
    <tr>
        <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">{{ trans('admin/users/table.manager') }}:</td>
        <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
        <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
        <td>_____________</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }}</td>
        <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
        <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
        <td></td>
    </tr>
</table>
<script src="{{ url(mix('js/dist/all.js')) }}" nonce="{{ csrf_token() }}"></script>
<script src="{{ url(mix('js/dist/bootstrap-table.js')) }}"></script>
<script src="{{ url(mix('js/dist/bootstrap-table-locale-all.min.js')) }}"></script>
<script src="{{ url(mix('js/dist/bootstrap-table-en-US.min.js')) }}"></script>
<script>
    $('.snipe-table').bootstrapTable('destroy').each(function () {
        data_export_options = $(this).attr('data-export-options');
        export_options = data_export_options ? JSON.parse(data_export_options) : {};
        export_options['htmlContent'] = false;
        export_options['jspdf']= {"orientation": "l"};
        export_options['onCellHtmlData'] = function (cell, rowIndex, colIndex, htmlData) {
            if (cell.is('th')) {
                return cell.find('.th-inner').text()
            }
            return htmlData
        }
        $(this).bootstrapTable({
            classes: 'table table-responsive table-no-bordered',
            ajaxOptions: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            stickyHeader: true,
            stickyHeaderOffsetLeft: parseInt($('body').css('padding-left'), 10),
            stickyHeaderOffsetRight: parseInt($('body').css('padding-right'), 10),
            undefinedText: '',
            iconsPrefix: 'fa',
            cookieStorage: '{{ config('session.bs_table_storage') }}',
            cookie: true,
            cookieExpire: '2y',
            mobileResponsive: true,
            maintainSelected: true,
            trimOnSearch: false,
            showSearchClearButton: true,
            paginationFirstText: "{{ trans('general.first') }}",
            paginationLastText: "{{ trans('general.last') }}",
            paginationPreText: "{{ trans('general.previous') }}",
            paginationNextText: "{{ trans('general.next') }}",
            pageList: ['10','20', '30','50','100','150','200'{!! ((config('app.max_results') > 200) ? ", '500'" : '') !!}{!! ((config('app.max_results') > 500) ? ", '".config('app.max_results')."'" : '') !!}],
            pageSize: {{  (($snipeSettings->per_page!='') && ($snipeSettings->per_page > 0)) ? $snipeSettings->per_page : 20 }},
            paginationVAlign: 'both',
            queryParams: function (params) {
                var newParams = {};
                for(var i in params) {
                    if(!keyBlocked(i)) {
                        newParams[i] = params[i];
                    }
                }
                return newParams;
            },
            formatLoadingMessage: function () {
                return '<h2><i class="fas fa-spinner fa-spin" aria-hidden="true"></i> {{ trans('general.loading') }} </h4>';
            },
            icons: {
                advancedSearchIcon: 'fas fa-search-plus',
                paginationSwitchDown: 'fa-caret-square-o-down',
                paginationSwitchUp: 'fa-caret-square-o-up',
                fullscreen: 'fa-expand',
                columns: 'fa-columns',
                refresh: 'fas fa-sync-alt',
                export: 'fa-download',
                clearSearch: 'fa-times'
            },
/*             exportOptions: export_options,
            exportTypes: ['xlsx', 'excel', 'csv', 'pdf','json', 'xml', 'txt', 'sql', 'doc' ],
            onLoadSuccess: function () {
                $('[data-tooltip="true"]').tooltip();
            } */
        });
    });
</script>
</body>
</html>