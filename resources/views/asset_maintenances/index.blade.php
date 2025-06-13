@extends('layouts/default')

{{-- Page title --}}
@section('title')
  {{ trans('admin/asset_maintenances/general.asset_maintenances') }}
  @parent
@stop


@section('header_right')
  @can('update', \App\Models\Asset::class)
    <a href="{{ route('maintenances.create') }}" class="btn btn-primary pull-right" style="margin-left: 10px;"> {{ trans('general.create') }}</a>
  @endcan
  <button id="printSelectedMaintenances" class="btn btn-success pull-right"><i class="fas fa-print"></i> {{ trans('general.print') }}</button>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">

          <table
              data-columns="{{ \App\Presenters\AssetMaintenancesPresenter::dataTableLayout() }}"
              data-cookie-id-table="maintenancesTable"
              data-pagination="true"
              data-search="true"
              data-side-pagination="server"
              data-show-columns="true"
              data-show-fullscreen="true"
              data-show-footer="true"
              data-show-export="true"
              data-show-refresh="true"
              data-click-to-select="true"
              data-maintain-meta-data="true"
              data-checkbox-header="true"
              data-sortable="true"
              data-filter-control="true"
              data-filter-show-clear="true"
              data-filter-control-visible="true" {{-- Force filter row always visible --}}
              id="maintenancesTable"
              class="table table-striped snipe-table"
              data-url="{{route('api.maintenances.index') }}"
              data-export-options='{
                "fileName": "export-maintenances-{{ date('Y-m-d') }}",
                    "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
              }'>

        </table>

      </div>
    </div>
  </div>
</div>
@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'maintenances-export', 'search' => true, 'filterControl' => true])
<script nonce="{{ csrf_token() }}">
    function maintenancesActions(value, row) {
        var actions = '<nobr>';
        if ((row) && (row.available_actions.update === true)) {
            let editUrl = "{{ route('maintenances.edit', ['maintenance' => ':MAINTENANCE_ID']) }}";
            editUrl = editUrl.replace(':MAINTENANCE_ID', row.id);
            actions += '<a href="' + editUrl + '" class="btn btn-sm btn-warning" data-tooltip="true" title="Update"><i class="fas fa-pencil-alt"></i></a>&nbsp;';
        }
        if ((row) && (row.available_actions.print === true)) {
            let printUrl = "{{ route('maintenances.print', ['assetId' => ':MAINTENANCE_ID']) }}";
            printUrl = printUrl.replace(':MAINTENANCE_ID', row.id);
            actions += '<a href="' + printUrl + '" class="btn btn-sm btn-primary" data-tooltip="true" title="Print"><i class="fas fa-print"></i></a>&nbsp;';
        }
        if ((row) && (row.available_actions.delete === true)) {
            let deleteUrl = "{{ route('maintenances.destroy', ['maintenance' => ':MAINTENANCE_ID']) }}";
            deleteUrl = deleteUrl.replace(':MAINTENANCE_ID', row.id);
            // Use JS variables for translations
            var sureToDelete = @json(trans('general.sure_to_delete'));
            var deleteTitle = @json(trans('general.delete'));
            actions += '<a href="' + deleteUrl + '" '
                + ' class="btn btn-danger btn-sm delete-asset"  data-tooltip="true"  '
                + ' data-toggle="modal" '
                + ' data-content="' + sureToDelete + ' ' + row.name + '?" '
                + ' data-title="' + deleteTitle + '" onClick="return false;">'
                + '<i class="fas fa-trash"></i></a>';
        }
        actions += '</nobr>';
        return actions;
    }

    $(function () {
        $('#printSelectedMaintenances').on('click', function() {
            var $table = $('#maintenancesTable');
            var selectedRows = $table.bootstrapTable('getSelections');

            if (!selectedRows || selectedRows.length === 0) {
                alert('Pilih minimal satu baris untuk diprint.');
                return;
            }

            // --- PDF filename logic ---
            var docDate = (new Date()).toISOString().slice(0,10); // yyyy-mm-dd
            var pdfFileName = 'Asset_Maintenances_' + docDate;

            // Set <title> for browser print-to-PDF
            var oldTitle = document.title;
            document.title = pdfFileName;

            // Create print area
            var $printArea = $('<div id="print-area"></div>');

            // Header logic - Adopting style from print.blade.php
            var header = '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">';
            header += '<div style="text-align: left;"><h2 style="margin: 0; font-size: 1.8em; color: #333;">SURAT JALAN</h2></div>';
            header += '<div style="text-align: right; font-size: 0.9em; color: #555;">{{ "Di Print pada:" }}: ' + new Date().toLocaleString() + '</div>';
            header += '</div>';
            $printArea.append(header);

            // Table
            var $tableClone = $('<table class="table table-striped" style="width:100%; margin-top:10px; border-collapse: collapse;"></table>');
            var tableHeader = '<thead><tr>' +
                '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f0f0f0; font-weight: bold;">' + @json(trans('admin/asset_maintenances/table.title')) + '</th>' +
                '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f0f0f0; font-weight: bold;">' + @json(trans('general.asset')) + '</th>' +
                '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f0f0f0; font-weight: bold;">' + @json(trans('admin/asset_maintenances/form.asset_maintenance_type')) + '</th>' +
                '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f0f0f0; font-weight: bold;">' + @json(trans('admin/asset_maintenances/form.start_date')) + '</th>' +
                //'<th style="border: 1px solid #ddd; padding: 8px; background-color: #f0f0f0; font-weight: bold;">' + @json(trans('admin/asset_maintenances/form.completion_date')) + '</th>' +
                //'<th style="border: 1px solid #ddd; padding: 8px; background-color: #f0f0f0; font-weight: bold; text-align: right;">' + @json(trans('admin/asset_maintenances/form.cost')) + '</th>' +
                '</tr></thead>';
            $tableClone.append(tableHeader);

            if (selectedRows && selectedRows.length > 0) {
                var tableBody = '<tbody>';
                $.each(selectedRows, function(index, row) {
                    tableBody += '<tr>';
                    tableBody += '<td style="border: 1px solid #ddd; padding: 8px;">' + (row.title || '') + '</td>';
                    // Compose asset info: name, tag, serial
                    var assetName = '';
                    if (row.asset && row.asset.name) {
                      assetName = row.asset.name;
                      if (row.asset.asset_tag) {
                        assetName += ' <span style="color:#888; font-size:90%;">[' + row.asset.asset_tag + ']</span>';
                      }
                      if (row.asset.serial) {
                        assetName += ' <span style="color:#888; font-size:90%;">(S/N: ' + row.asset.serial + ')</span>';
                      }
                    } else if (row.asset_name) {
                      assetName = row.asset_name;
                    }
                    tableBody += '<td style="border: 1px solid #ddd; padding: 8px;">' + assetName + '</td>';
                    // Fix supplier object display
                    var supplierName = '';
                    if (row.supplier) {
                      supplierName = (typeof row.supplier === 'object' && row.supplier.name) ? row.supplier.name : row.supplier;
                    }
                    tableBody += '<td style="border: 1px solid #ddd; padding: 8px;">' + 
                      (row.asset_maintenance_type || '') + 
                      (supplierName ? ' - ' + supplierName : '') + 
                      '</td>';
                    tableBody += '<td style="border: 1px solid #ddd; padding: 8px;">' + (row.start_date ? (row.start_date.formatted || row.start_date) : '') + '</td>';
                    //tableBody += '<td style="border: 1px solid #ddd; padding: 8px;">' + (row.completion_date ? (row.completion_date.formatted || row.completion_date) : '') + '</td>';
                    //tableBody += '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' + (row.cost ? row.cost.raw : '') + '</td>';
                    tableBody += '</tr>';
                });
                tableBody += '</tbody>';
                $tableClone.append(tableBody);
                $printArea.append($tableClone);

                // Blok tanda tangan tanpa border tabel
                var signatureBlock = '<table style="margin-top: 80px; width:100%; border:none;">' +
                    '<tr>' +
                        '<td style="padding-right: 10px; vertical-align: top; font-weight: bold; border:none;">Pengirim:</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">______________________________________</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">______________________________________</td>' +
                        '<td style="border:none;">_____________</td>' +
                    '</tr>' +
                    '<tr style="height: 80px;">' +
                        '<td style="border:none;"></td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.name')) + '</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.signature')) + '</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.date')) + '</td>' +
                    '</tr>' +
                    '<tr>' +
                        '<td style="padding-right: 10px; vertical-align: top; font-weight: bold; border:none;">Atasan:</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">______________________________________</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">______________________________________</td>' +
                        '<td style="border:none;">_____________</td>' +
                    '</tr>' +
                    '<tr style="height: 80px;">' +
                        '<td style="border:none;"></td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.name')) + '</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.signature')) + '</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.date')) + '</td>' +
                        '<td style="border:none;"></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<td style="padding-right: 10px; vertical-align: top; font-weight: bold; border:none;">Supplier:</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">______________________________________</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">______________________________________</td>' +
                        '<td style="border:none;">_____________</td>' +
                    '</tr>' +
                    '<tr>' +
                        '<td style="border:none;"></td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.name')) + '</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.signature')) + '</td>' +
                        '<td style="padding-right: 10px; vertical-align: top; border:none;">' + @json(trans('general.date')) + '</td>' +
                        '<td style="border:none;"></td>' +
                    '</tr>' +
                '</table>';
                $printArea.append(signatureBlock);

                
            } else {
                $printArea.append('<div style="text-align:center; margin:40px 0; font-size:1.2em;">' + @json(trans('general.no_results_found')) + '</div>');
            }

            // Print CSS - Adopting styles from print.blade.php
            var printCSS = '<style type="text/css" media="print">' +
                '@page { size: A4; margin: 20mm; }' +
                // Base body styles for print media
                'body { margin:0; padding:0; font-family: "Arial, Helvetica", sans-serif; font-size: 10pt; color: #333; background: white !important; }' +
                // Hide all direct children of body except #print-area itself
                'body > *:not(#print-area) { display: none !important; }' +
                // Ensure #print-area is visible and takes up space
                '#print-area { display: block !important; }' +
                // Styles for content specifically within #print-area
                '#print-area h2 { font-size: 1.8em; margin: 0; color: #333; }' +
                '#print-area table { width: 100%; border-collapse: collapse; margin-top: 15px; }' +
                '#print-area th, #print-area td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }' +
                '#print-area th { background-color: #f0f0f0; font-weight: bold; color: #444; }' +
                '#print-area tbody tr:nth-child(even) { background-color: #f9f9f9; }' +
                // Fallback for any elements that might have .no-print class
                '.no-print, .no-print * { display: none !important; }' +
                '</style>';

            // Remove any existing print area and styles to avoid conflicts
            $('#print-area, style[media="print"]').remove();

            $('head').append(printCSS);
            $('body').append($printArea);
            $printArea.show(); // Ensure it's visible for printing

            window.print();

            // Clean up after printing
            document.title = oldTitle;
            $printArea.remove();
            $('style[media="print"]').remove();

        });
    });
</script>
@stop
