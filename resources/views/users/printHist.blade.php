<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @if ((isset($users) && count($users) === 1))
        <title>{{ trans('general.assigned_to', ['name' => $users[0]->present()->fullName()]) }} - {{ date('Y-m-d H:i', time()) }}</title>
    @else
        <title>{{ trans('admin/users/general.print_history') }} - {{ date('Y-m-d H:i', time()) }}</title>
    @endisset

    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">

    <link rel="stylesheet" href="{{ url(mix('css/dist/bootstrap-table.css')) }}">

    {{-- stylesheets --}}
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
            border: 1px solid #d3d3d3;
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

{{-- If we are rendering multiple users we'll add the ability to show/hide EULAs for all of them at once via this button --}}
@if (count($users) > 1)
    <div class="pull-right hidden-print">
        <span>{{ trans('general.show_or_hide_eulas') }}</span>
        <button class="btn btn-default" type="button" data-toggle="collapse" data-target=".eula-row" aria-expanded="false" aria-controls="eula-row" title="EULAs">
            <i class="fa fa-eye-slash"></i>
        </button>
    </div>
@endif

@if ($snipeSettings->logo_print_assets=='1')
    <div style="text-align:center; margin-bottom: 20px;">
        @if ($snipeSettings->brand == '3')
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}"><br>
            @endif
            <span style="font-size:2em; font-weight:bold; display:block; margin-top:10px;">IT Asset Return Form</span>
            <!-- {{ $snipeSettings->site_name }} -->
        @elseif ($snipeSettings->brand == '2')
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}"><br>
            @endif
        @else
            <span style="font-size:2em; font-weight:bold; display:block; margin-top:10px;">IT Asset Return Form</span>
            <!-- <h2>{{ $snipeSettings->site_name }}</h2> -->
        @endif
    </div>
@endif

@foreach ($users as $show_user)
    <div id="start_of_user_section"> {{-- used for page breaks when printing --}}</div>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="text-align: left;">
            <h2>
                {{ trans('general.assigned_to', ['name' => $show_user->present()->fullName()]) }}
                {{ ($show_user->employee_num!='') ? ' (#'.$show_user->employee_num.') ' : '' }}
                {{ ($show_user->jobtitle!='' ? ' - '.$show_user->jobtitle : '') }}
            </h2>
        </div>
        <div style="text-align: right;">
            {{ trans('Printed On: ')}} {{ Helper::getFormattedDateObject(now(), 'datetime', false) }}
        </div>
    </div>
    <p></p>
    @if (isset($assets) && $assets->count() > 0)
        @php $counter = 1; @endphp
        <table class="snipe-table table table-striped inventory" style="margin-top:30px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('admin/hardware/table.asset_tag') }}</th>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans('general.category') }}</th>
                    <th>{{ trans('admin/hardware/form.model') }}</th>
                    <th>{{ trans('admin/hardware/form.serial') }}</th>
                    <th>{{ trans('admin/hardware/table.checkout_date') }}</th>
                    <th>{{ trans('admin/hardware/table.checkin_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $asset)
                    <tr>
                        <td>{{ $counter }}</td>
                        <td>{{ $asset->asset_tag }}</td>
                        <td>{{ $asset->name }}</td>
                        <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                        <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                        <td>{{ $asset->serial }}</td>
                        <td>{{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                        <td>{{ Helper::getFormattedDateObject($asset->last_checkin, 'datetime', false) }}</td>
                    </tr>
                    @php $counter++; @endphp
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align:center; margin:40px 0; font-size:1.2em;">No checked-in assets found which {{ trans('general.assigned_to', ['name' => $show_user->present()->fullName()]) }}</div>
    @endif

    @php
        if (!empty($eulas)) $eulas = array_unique($eulas);
    @endphp
    {{-- This may have been render at the top of the page if we're rendering more than one user... --}}
    @if (count($users) === 1 && !empty($eulas))
        <p></p>
        <div class="pull-right">
            <button class="btn btn-default hidden-print" type="button" data-toggle="collapse" data-target=".eula-row" aria-expanded="false" aria-controls="eula-row" title="EULAs">
                <i class="fa fa-eye-slash"></i>
            </button>
        </div>
    @endif

    <table style="margin-top: 80px;">
        @if (!empty($eulas))
        <tr class="collapse eula-row">
            <!--<td style="padding-right: 10px; vertical-align: top; font-weight: bold;">EULA</td>-->
            <td style="padding-right: 10px;text-align:center; vertical-align: top; padding-bottom: 50px;" colspan="3">
                @foreach ($eulas as $key => $eula)
                    {!! $eula !!}
                @endforeach
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">Received/<p></p>Acknowledged By: <!--{{ trans('general.signed_off_by') }}:--></td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr style="height: 80px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }} / Position</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
        </tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">Issued/ Installed By: <!--{{ trans('admin/users/table.manager') }}:--></td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr style="height: 80px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }} / Position</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
            <td></td>
        </tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">Noted by: <!--{{ trans('general.signed_off_by') }}:--></td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr style="height: 80px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }} / Position</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
        </tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">Filed By: <!--{{ trans('admin/users/table.manager') }}:--></td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }} / Position</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
            <td></td>
        </tr>

    </table>
@endforeach

{{-- Javascript files --}}
<script src="{{ url(mix('js/dist/all.js')) }}" nonce="{{ csrf_token() }}"></script>

<script src="{{ url(mix('js/dist/bootstrap-table.js')) }}"></script>
<script src="{{ url(mix('js/dist/bootstrap-table-locale-all.min.js')) }}"></script>

<!-- load english again here, even though it's in the all.js file, because if BS table doesn't have the translation, it otherwise defaults to chinese. See https://bootstrap-table.com/docs/api/table-options/#locale -->
<script src="{{ url(mix('js/dist/bootstrap-table-en-US.min.js')) }}"></script>

<script>
    $('.snipe-table').bootstrapTable('destroy').each(function () {
        console.log('BS table loaded');

        data_export_options = $(this).attr('data-export-options');
        export_options = data_export_options ? JSON.parse(data_export_options) : {};
        export_options['htmlContent'] = false; // this is already the default; but let's be explicit about it
        export_options['jspdf']= {"orientation": "l"};
        // the following callback method is necessary to prevent XSS vulnerabilities
        // (this is taken from Bootstrap Tables's default wrapper around jQuery Table Export)
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
            // reorderableColumns: true,
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
            pageList: ['10','20', '30','50','100','150','200'{!! ((config('app.max_results') > 200) ? ",'500'" : '') !!}{!! ((config('app.max_results') > 500) ? ",'".config('app.max_results')."'" : '') !!}],
            pageSize: {{  (($snipeSettings->per_page!='') && ($snipeSettings->per_page > 0)) ? $snipeSettings->per_page : 20 }},
            paginationVAlign: 'both',
            queryParams: function (params) {
                var newParams = {};
                for(var i in params) {
                    if(!keyBlocked(i)) { // only send the field if it's not in blockedFields
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
            exportOptions: export_options,

            exportTypes: ['xlsx', 'excel', 'csv', 'pdf','json', 'xml', 'txt', 'sql', 'doc' ],
            onLoadSuccess: function () {
                $('[data-tooltip="true"]').tooltip(); // Needed to attach tooltips after ajax call
            }

        });
    });
</script>

</body>
</html>
