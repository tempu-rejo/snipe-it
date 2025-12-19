<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @if ((isset($users) && count($users) === 1))
        <title>{{ trans('general.assigned_to', ['name' => $users[0]->present()->fullName()]) }} - {{ date('Y-m-d H:i', time()) }}</title>
    @else
        <title>{{ trans('admin/users/general.print_assigned') }} - {{ date('Y-m-d H:i', time()) }}</title>
    @endisset

    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">

    <link rel="stylesheet" href="{{ url(mix('css/dist/bootstrap-table.css')) }}">

    {{-- stylesheets --}}
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">
    
    {{-- html2pdf library untuk auto-generate PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
            table-layout: auto;
            word-wrap: break-word;
        }
        
        table.inventory th,
        table.inventory td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 150px;
        }
        
        /* Prevent table from being cut */
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }

        @page {
            size: A4;
            margin: 10mm;
        }
        
        .print-logo {
            max-height: 40px;
        }

        h4 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        /* Page break between users */
        .user-section {
            page-break-after: always;
        }
        
        .user-section:last-child {
            page-break-after: auto;
        }
        
        /* Repeat header on each page */
        .page-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Hide all users except the one being printed */
        @media print {
            body.print-single-user .user-section {
                display: none !important;
            }
            
            body.print-single-user .user-section.print-active {
                display: block !important;
                page-break-after: auto;
            }
            
            body.print-single-user .print-controls {
                display: none !important;
            }
            
            /* Hide overlay/tips saat print */
            #auto-print-overlay {
                display: none !important;
            }
        }


    </style>


</head>
<body>

{{-- If we are rendering multiple users we'll add the ability to show/hide EULAs for all of them at once via this button --}}
@if (count($users) > 1)
    <div class="pull-right hidden-print print-controls">
        <span>{{ trans('general.show_or_hide_eulas') }}</span>
        <button class="btn btn-default" type="button" data-toggle="collapse" data-target=".eula-row show" aria-expanded="false" aria-controls="eula-row" title="EULAs">
            <i class="fa fa-eye-slash"></i>
        </button>
    </div>
@endif

@foreach ($users as $index => $show_user)
    <div class="user-section" id="user-section-{{ $index }}" data-user-name="{{ $show_user->present()->fullName() }}">
        <!-- Header untuk setiap user -->
        <div class="page-header">
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}" style="max-height: 60px; margin-bottom: 15px;"><br>
            @endif
            @if (isset($print_type) && $print_type === 'print_assigned')
                <h3 style="margin: 10px 0; font-size: 1.3em; font-weight: bold;">IT Asset Accountability Form</h3>
            @else
                <h3 style="margin: 10px 0; font-size: 1.3em; font-weight: bold;">Annual IT Asset Acknowledgement and Confirmation</h3>
                <h4 style="margin: 5px 0; font-size: 1.1em; font-weight: bold;">YEAR {{ date('Y') }}</h4>
            @endif
        </div>

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

    
    

    @if ($show_user->assets->count() > 0)
        @php
            $counter = 1;
        @endphp

        <div id="assets-toolbar-{{ $index }}">
            <h4>{{ trans_choice('general.countable.assets', $show_user->assets->count(), ['count' => $show_user->assets->count()]) }}
            </h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="AssetsAssigned-{{ $index }}"
            data-pagination="false"
            data-id-table="AssetsAssigned-{{ $index }}"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-toolbar="#assets-toolbar-{{ $index }}"
            data-show-columns="true"
            data-sort-order="asc"
            data-sort-name="#"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="AssetsAssigned">
            <thead>
                <th data-field="asset_id" data-sortable="True" data-visible="true" data-switchable="false">#</th>
                <th data-field="asset_image" data-sortable="true" data-visible="false" data-switchable="true">{{ trans('general.image') }}</th>
                <th data-field="asset_tag" data-sortable="true" data-visible="true" data-switchable="false">{{ trans('admin/hardware/table.asset_tag') }}</th>
                <th data-field="asset_name" data-sortable="true" data-visible="true">{{ trans('general.name') }}</th>
                <th data-field="asset_category" data-sortable="true" data-visible="true">{{ trans('general.category') }}</th>
                <th data-field="asset_model" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.model') }}</th>
                <th data-field="rtd_location" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.default_location') }}</th>
                <th data-field="asset_location" data-sortable="true" data-visible="false">{{ trans('general.location') }}</th>
                <th data-field="asset_serial" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.serial') }}</th>
                <th data-field="asset_checkout_date" data-sortable="true" data-visible="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th data-field="signature" data-sortable="true" data-visible="true">{{ trans('general.signature') }}</th>
            </thead>
            <tbody>
            @foreach ($show_user->assets as $asset)
                @php
                    if (($asset->model->category) && ($asset->model->category->getEula())) $eulas[] = $asset->model->category->getEula()
                @endphp
                <tr>
                    <td>{{ $counter }}</td>
                    <td>
                        @if ($asset->getImageUrl())
                            <img src="{{ $asset->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                        @endif
                    </td>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                    <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                    <td>{{ ($asset->defaultLoc) ? $asset->defaultLoc->name : '' }}</td>
                    <td>{{ ($asset->location) ? $asset->location->name : '' }}</td>
                    <td>{{ $asset->serial }}</td>
                    <td>
                        {{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                    <td>
                        @if (($asset->assetlog->first()) && ($asset->assetlog->first()->accept_signature!=''))
                            <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $asset->assetlog->first()->accept_signature }}">
                        @endif
                    </td>
                </tr>
                @if ($settings->show_assigned_assets)
                    @php
                        $assignedCounter = 1;
                    @endphp
                    @foreach ($asset->assignedAssets as $asset)
                        <tr>
                            <td>{{ $counter }}.{{ $assignedCounter }}</td>
                            <td>
                                @if ($asset->getImageUrl())
                                    <img src="{{ $asset->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                                @endif
                            </td>
                            <td>{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                            <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                            <td>{{ ($asset->defaultLoc) ? $asset->defaultLoc->name : '' }}</td>
                            <td>{{ ($asset->location) ? $asset->location->name : '' }}</td>
                            <td>{{ $asset->serial }}</td>
                            <td>
                                {{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                            <td>
                                @if (($asset->assetlog->first()) && ($asset->assetlog->first()->accept_signature!=''))
                                    <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $asset->assetlog->first()->accept_signature }}">
                                @endif
                            </td>
                        </tr>
                        @php
                            $assignedCounter++
                        @endphp
                    @endforeach
                @endif
                @php
                    $counter++
                @endphp
            @endforeach
            </tbody>
        </table>
    @endif

    @if ($show_user->licenses->count() > 0)
        <div id="licenses-toolbar-{{ $index }}">
            <h4>{{ trans_choice('general.countable.licenses', $show_user->licenses->count(), ['count' => $show_user->licenses->count()]) }}</h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="licensessAssigned-{{ $index }}"
            data-toolbar="#licenses-toolbar-{{ $index }}"
            data-pagination="false"
            data-id-table="licensessAssigned-{{ $index }}"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="licensessAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false">#</th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('admin/licenses/form.license_key') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
            </tr>
            </thead>
            @php
                $lcounter = 1;
            @endphp

            @foreach ($show_user->licenses as $license)
                @php
                    if (($license->category) && ($license->category->getEula())) $eulas[] = $license->category->getEula()
                @endphp
                <tr>
                    <td>{{ $lcounter }}</td>
                    <td>{{ $license->name }}</td>
                    <td>
                        @can('viewKeys', $license)
                            {{ $license->serial }}
                        @else
                            <i class="fa-lock" aria-hidden="true"></i> {{ str_repeat('x', 15) }}
                        @endcan
                    </td>
                    <td>{{  $license->pivot->updated_at }}</td>
                </tr>
                @php
                    $lcounter++
                @endphp
            @endforeach
        </table>
    @endif


    @if ($show_user->accessories->count() > 0)
        <div id="accessories-toolbar-{{ $index }}">
            <h4>{{ trans_choice('general.countable.accessories', $show_user->accessories->count(), ['count' => $show_user->accessories->count()]) }}</h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="accessoriesAssigned-{{ $index }}"
            data-toolbar="#accessories-toolbar-{{ $index }}"
            data-pagination="false"
            data-id-table="accessoriesAssigned-{{ $index }}"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="accessoriesAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false">#</th>
                <th data-field="accessory_image" data-sortable="true"  data-visible="true">{{ trans('general.image') }}</th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('general.category') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('general.signature') }}</th>
            </tr>
            </thead>
            @php
                $acounter = 1;
            @endphp

            @foreach ($show_user->accessories as $accessory)
                @if ($accessory)
                    @php
                        if (($accessory->category) && ($accessory->category->getEula())) $eulas[] = $accessory->category->getEula()
                    @endphp
                    <tr>
                        <td>{{ $acounter }}</td>
                        <td>
                            @if ($accessory->getImageUrl())
                                <img src="{{ $accessory->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                            @endif
                        </td>
                        <td>{{ ($accessory->manufacturer) ? $accessory->manufacturer->name : '' }} {{ $accessory->name }} {{ $accessory->model_number }}</td>
                        <td>{{ $accessory->category->name }}</td>
                        <td>{{ $accessory->pivot->created_at }}</td>

                        <td>
                            @if (($accessory->assetlog->first()) && ($accessory->assetlog->first()->accept_signature!=''))
                                <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $accessory->assetlog->first()->accept_signature }}">
                            @endif
                        </td>
                    </tr>
                    @php
                        $acounter++
                    @endphp
                @endif
            @endforeach
        </table>
    @endif

    @if ($show_user->consumables->count() > 0)
        <div id="consumables-toolbar-{{ $index }}">
            <h4>{{ trans_choice('general.countable.consumables', $show_user->consumables->count(), ['count' => $show_user->consumables->count()]) }}</h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="consumablesAssigned-{{ $index }}"
            data-pagination="false"
            data-toolbar="#consumables-toolbar-{{ $index }}"
            data-id-table="consumablesAssigned-{{ $index }}"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="consumablesAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false"></th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('general.category') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('general.signature') }}</th>

            </tr>
            </thead>
            @php
                $ccounter = 1;
            @endphp

            @foreach ($show_user->consumables as $consumable)
                @if ($consumable)
                    @php
                        if (($consumable->category) && ($consumable->category->getEula())) $eulas[] = $consumable->category->getEula()
                    @endphp
                    <tr>
                        <td>{{ $ccounter }}</td>
                        <td>
                        @if ($consumable->deleted_at!='')
                            <td>{{ ($consumable->manufacturer) ? $consumable->manufacturer->name : '' }}  {{ $consumable->name }} {{ $consumable->model_number }}</td>
                            @else
                                {{ ($consumable->manufacturer) ? $consumable->manufacturer->name : '' }}  {{ $consumable->name }} {{ $consumable->model_number }}
                            @endif
                            </td>
                            <td>{{ ($consumable->category) ? $consumable->category->name : ' invalid/deleted category' }} </td>
                            <td>{{  $consumable->pivot->created_at }}</td>
                            <td>
                                @if (($consumable->assetlog->first()) && ($consumable->assetlog->first()->accept_signature!=''))
                                    <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $consumable->assetlog->first()->accept_signature }}">
                                @endif
                            </td>
                    </tr>
                    @php
                        $ccounter++
                    @endphp
                @endif
            @endforeach
        </table>
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
        <p></p>

        <p style="text-align: center; vertical-align: top; font-weight: bold; text-decoration: underline; font-size: 1.3em;">
            Statement of Accountability 
        </p>
        <p></p>
        <p style="text-align: center; vertical-align: top;">
            I acknowledge receipt of the listed items and accept responsibility for their care and proper use. I will use them only for work, return them when required, and may be held liable for any loss or damage unless proven not my fault.
        </p>
        <p style="text-align: center; vertical-align: top; font-weight: bold; text-decoration: underline; font-size: 1.3em;">
            IT Asset Policy
        </p>
            <p style="text-align: center; vertical-align: top;">By signing, I agree to these terms and the company’s IT Asset Policy.</p>
        <p></p>

        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">Employee: </td>
            <td style="padding-right: 10px; vertical-align: top;border-bottom: 1px solid black; width: 30%;">&#8203;{{ $show_user->present()->fullName() }}</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr style="height: 80px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
        </tr>
        
        @if (isset($print_type) && $print_type === 'print_assigned')
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">IT Personel:</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr style="height: 80px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.name') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.signature') }}</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
            <td></td>
        </tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">IT Manager: </td>
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
        @endif


    </table>
    </div> {{-- End user-section --}}
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

    // Handle individual user print
    $(document).ready(function() {
        // Auto print mode - untuk bulk print all users
        var autoPrintMode = {{ count($users) > 1 ? 'true' : 'false' }};
        var currentPrintIndex = 0;
        var totalUsers = {{ count($users) }};
        var printQueue = [];
        var isPrinting = false;
        
        @if (count($users) > 1)
            // Populate print queue dengan semua user
            @foreach ($users as $index => $show_user)
                printQueue.push({
                    index: {{ $index }},
                    name: '{{ $show_user->present()->fullName() }}'
                });
            @endforeach
        @endif
        
        // Function untuk auto-generate PDF tanpa dialog
        function generatePDFAuto(userIndex, userName, isAutoPrint) {
            if (isPrinting) return;
            isPrinting = true;
            
            var currentYear = new Date().getFullYear();
            var printType = '{{ $print_type ?? "print_annual" }}';
            var pdfFileName = '';
            if (printType === 'print_assigned') {
                pdfFileName = 'IT_Asset_Accountability_' + userName.replace(/\s+/g, '_') + '_' + currentYear;
            } else {
                pdfFileName = 'Annual_IT_Asset_' + userName.replace(/\s+/g, '_') + '_' + currentYear;
            }
            
            // Update progress
            $('#print-progress').html('Generating PDF for ' + userName + '...<br>(' + (currentPrintIndex + 1) + ' of ' + totalUsers + ')');
            
            // Show only current user
            $('body').addClass('print-single-user');
            $('.user-section').removeClass('print-active');
            $('#user-section-' + userIndex).addClass('print-active');
            
            // Get the active user section element
            var element = document.getElementById('user-section-' + userIndex);
            
            // PDF options
            var opt = {
                margin: [10, 10, 10, 10],
                filename: pdfFileName + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    logging: false
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait' 
                },
                pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
            };
            
            // Generate PDF
            html2pdf().set(opt).from(element).save().then(function() {
                // PDF saved successfully
                setTimeout(function() {
                    $('body').removeClass('print-single-user');
                    $('.user-section').removeClass('print-active');
                    isPrinting = false;
                    
                    // Jika auto print mode dan masih ada user lain
                    if (isAutoPrint && currentPrintIndex < totalUsers - 1) {
                        currentPrintIndex++;
                        var nextUser = printQueue[currentPrintIndex];
                        
                        // Update progress
                        $('#print-progress').html('✓ PDF ' + currentPrintIndex + ' completed!<br>Preparing next user...');
                        
                        // Delay sebelum generate PDF berikutnya
                        setTimeout(function() {
                            generatePDFAuto(nextUser.index, nextUser.name, true);
                        }, 1500);
                    } else if (isAutoPrint && currentPrintIndex >= totalUsers - 1) {
                        // Semua PDF sudah di-generate
                        $('#auto-print-overlay').html(
                            '<div style="text-align: center;">' +
                            '<h2 style="color: #4CAF50;">✓ Completed!</h2>' +
                            '<p style="font-size: 20px; margin: 20px 0;">Semua ' + totalUsers + ' PDF telah berhasil di-generate dan di-download!</p>' +
                            '<p>Silakan cek folder Downloads Anda.</p>' +
                            '<button class="btn btn-primary btn-lg" onclick="window.location.href=\'{{ route("users.index") }}\'" style="margin-top: 20px; padding: 15px 40px; font-size: 16px;">Kembali ke Users List</button>' +
                            '</div>'
                        );
                    }
                }, 500);
            }).catch(function(error) {
                console.error('Error generating PDF:', error);
                $('#print-progress').html('❌ Error generating PDF for ' + userName + '<br>Skipping to next...');
                isPrinting = false;
                
                // Lanjut ke user berikutnya meskipun error
                if (isAutoPrint && currentPrintIndex < totalUsers - 1) {
                    currentPrintIndex++;
                    var nextUser = printQueue[currentPrintIndex];
                    setTimeout(function() {
                        generatePDFAuto(nextUser.index, nextUser.name, true);
                    }, 2000);
                }
            });
        }
        
        // Manual print button handler - masih menggunakan browser print dialog
        $('.print-user-btn').on('click', function() {
            var userIndex = $(this).data('user-index');
            var userName = $(this).data('user-name');
            
            // Show only current user
            $('body').addClass('print-single-user');
            $('.user-section').removeClass('print-active');
            $('#user-section-' + userIndex).addClass('print-active');
            
            var currentYear = new Date().getFullYear();
            var printType = '{{ $print_type ?? "print_annual" }}';
            var pdfFileName = '';
            if (printType === 'print_assigned') {
                pdfFileName = 'IT_Asset_Accountability_' + userName.replace(/\s+/g, '_') + '_' + currentYear;
            } else {
                pdfFileName = 'Annual_IT_Asset_' + userName.replace(/\s+/g, '_') + '_' + currentYear;
            }
            var originalTitle = document.title;
            document.title = pdfFileName;
            
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    $('body').removeClass('print-single-user');
                    $('.user-section').removeClass('print-active');
                    document.title = originalTitle;
                }, 500);
            }, 300);
        });
        
        // Auto-trigger PDF generation untuk semua user jika multiple users
        @if (count($users) > 1)
            if (autoPrintMode && printQueue.length > 0) {
                // Tampilkan pesan loading
                $('body').prepend('<div id="auto-print-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white;">' +
                    '<div style="text-align: center; padding: 50px; background: rgba(255,255,255,0.1); border-radius: 15px; min-width: 500px;">' +
                    '<h2 style="font-size: 28px; margin-bottom: 20px;">🎯 Auto-Generating PDFs</h2>' +
                    '<div style="margin: 30px 0;">' +
                    '<div style="width: 100%; height: 8px; background: rgba(255,255,255,0.2); border-radius: 4px; overflow: hidden;">' +
                    '<div id="progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #4CAF50, #8BC34A); transition: width 0.5s;"></div>' +
                    '</div>' +
                    '</div>' +
                    '<p id="print-progress" style="font-size: 18px; margin: 20px 0; min-height: 50px;">Starting...</p>' +
                    '<p style="font-size: 14px; opacity: 0.8; margin-top: 30px;">⏱️ Proses ini berjalan otomatis<br>File PDF akan langsung terdownload ke folder Downloads</p>' +
                    '</div>' +
                    '</div>');
                
                // Mulai auto-generate setelah page fully loaded
                setTimeout(function() {
                    $('#print-progress').html('Initializing PDF generator...');
                    // Update progress bar
                    var progress = ((currentPrintIndex + 1) / totalUsers) * 100;
                    $('#progress-bar').css('width', progress + '%');
                    
                    setTimeout(function() {
                        generatePDFAuto(printQueue[0].index, printQueue[0].name, true);
                    }, 1000);
                }, 2000);
                
                // Update progress bar setiap kali index berubah
                setInterval(function() {
                    var progress = ((currentPrintIndex + 1) / totalUsers) * 100;
                    $('#progress-bar').css('width', progress + '%');
                }, 500);
            }
        @endif
    });
</script>

</body>
</html>
