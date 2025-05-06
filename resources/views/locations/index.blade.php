@extends('layouts.default')

@section('title')
    {{ trans('general.locations') }}
@endsection

@section('breadcrumb')
    <li class="active">{{ trans('general.locations') }}</li>
@endsection

@section('header_right')
  @can('create', \App\Models\Location::class)
      <a href="{{ route('locations.create') }}" class="btn btn-primary pull-right">
  {{ trans('general.create') }}</a>
  @endcan
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">
          @include('partials.locations-bulk-actions')

          <table
                  data-columns="{{ \App\Presenters\LocationPresenter::dataTableLayout() }}"
                  data-cookie-id-table="locationTable"
                  data-click-to-select="true"
                  data-pagination="true"
                  data-id-table="locationTable"
                  data-toolbar="#locationsBulkEditToolbar"
                  data-bulk-button-id="#bulkLocationsEditButton"
                  data-bulk-form-id="#locationsBulkForm"
                  data-search="true"
                  data-side-pagination="server"
                  data-show-columns="true"
                  data-show-fullscreen="true"
                  data-show-export="true"
                  data-show-refresh="true"
                  data-sort-order="asc"
                  id="locationTable"
                  class="table table-striped snipe-table"
                  data-url="{{ route('api.locations.index') }}"
                  data-export-options='{
              "fileName": "export-locations-{{ date('Y-m-d') }}",
              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
              }'>
          </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'locations-export', 'search' => true])

@endsection
