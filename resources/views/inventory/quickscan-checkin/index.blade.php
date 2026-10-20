@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Quick Scan CheckIN Transactions
    @parent
@stop

@section('header_right')
  <a href="{{ route('inventory.quickscan-checkin.create') }}" class="btn btn-primary pull-right">
    <i class="fas fa-plus" aria-hidden="true"></i>
    {{ trans('general.create') }}
  </a>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            
            <table
              data-advanced-search="true"
              data-click-to-select="true"
              data-columns='[
                {
                  "field": "document_number",
                  "title": "No. Dokumen",
                  "searchable": true,
                  "sortable": true
                },
                {
                  "field": "transaction_date",
                  "title": "Tanggal",
                  "searchable": true,
                  "sortable": true
                },
                {
                  "field": "user_name",
                  "title": "User",
                  "searchable": true,
                  "sortable": true
                },
                {
                  "field": "items_count",
                  "title": "Jumlah Asset",
                  "searchable": false,
                  "sortable": true
                },
                {
                  "field": "created_by_name",
                  "title": "Dibuat Oleh",
                  "searchable": true,
                  "sortable": true
                },
                {
                  "field": "created_at",
                  "title": "Tanggal Dibuat",
                  "searchable": true,
                  "sortable": true
                },
                {
                  "field": "actions",
                  "title": "{{ trans('table.actions') }}",
                  "searchable": false,
                  "sortable": false
                }
              ]'
              data-cookie-id-table="quickScanCheckinListingTable"
              data-pagination="true"
              data-id-table="quickScanCheckinListingTable"
              data-search="true"
              data-side-pagination="server"
              data-show-columns="true"
              data-show-export="true"
              data-show-footer="true"
              data-show-refresh="true"
              data-sort-order="desc"
              data-sort-name="created_at"
              data-show-fullscreen="true"
              id="quickScanCheckinListingTable"
              class="table table-striped snipe-table"
              data-url="{{ route('inventory.quickscan-checkin.data') }}"
              data-export-options='{
                "fileName": "export-quickscan-checkin-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions"]
              }'>
            </table>
            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- ./box-body -->
    </div><!-- /.box -->
  </div>
</div>

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table')

@endsection
