@extends('layouts/default')

{{-- Page title --}}
@section('title')
  @if ($item->id)
    {{ trans('admin/asset_maintenances/form.update') }}
  @else
    {{ trans('admin/asset_maintenances/form.create') }}
  @endif
  @parent
@stop


@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop


{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-9">
    @if ($item->id)
      <form class="form-horizontal" method="post" action="{{ route('maintenances.update', $item->id) }}" autocomplete="off" enctype="multipart/form-data">
      {{ method_field('PUT') }}
    @else
      <form class="form-horizontal" method="post" action="{{ route('maintenances.store') }}" autocomplete="off" enctype="multipart/form-data">
    @endif
    <!-- CSRF Token -->
    {{ csrf_field() }}

    <div class="box box-default">
      <div class="box-header with-border">
        <h2 class="box-title">
          @if ($item)
          {{ $item->name }}
          @endif
        </h2>
      </div><!-- /.box-header -->

      <div class="box-body">

        @if (session('partial_success'))
          <div class="alert alert-warning">
            {!! session('partial_success') !!}
          </div>
        @endif
        @if (session('partial_error_detail'))
          <div class="alert alert-danger">
            <b>Detail error:</b><br>
            <ul>
              @foreach (session('partial_error_detail') as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif        

        <!-- Document Number (Read Only) -->
        <div class="form-group">
          <label class="col-md-3 control-label">
            No. Dokumen
          </label>
          <div class="col-md-7">
            <p class="form-control-static">
              <strong>{{ $item->document_number }}</strong>
              @if(!$item->document_number)
                <span class="text-muted">(akan dibuat otomatis)</span>
              @endif
            </p>
          </div>
        </div>

        <!-- Title -->
        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
          <label for="title" class="col-md-3 control-label">
            {{ trans('admin/asset_maintenances/form.title') }}
          </label>
          <div class="col-md-7">
            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', $item->title) }}"{{  (Helper::checkIfRequired($item, 'title')) ? ' required' : '' }}{{ $item->id ? ' disabled' : '' }} />
            @if($item->id && $item->title)
                <!-- Hidden input to ensure value is submitted when field is disabled -->
                <input type="hidden" name="title" value="{{ $item->title }}">
            @endif
            {!! $errors->first('title', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>        @include ('partials.forms.edit.asset-select', [
            'translated_name' => trans('general.asset'),
            'fieldname' => 'asset_id',
            'required' => 'true',
            'multiple' => !$item->id, // enable multi selection only for new records
            'disabled' => $item->id ? true : false, // disable when editing
            'asset_status_type' => 'undeployable' // only show undeployable assets for maintenance
        ])

        @include ('partials.forms.edit.supplier-select', [
            'translated_name' => trans('general.supplier'), 
            'fieldname' => 'supplier_id', 
            'required' => 'true',
            'disabled' => $item->id ? true : false, // disable when editing
            'hide_new' => $item->id ? 'true' : 'false' // hide "New supplier" button when editing
        ])

        <!-- Asset Maintenance Type (from Status Labels) -->
        <div class="form-group {{ $errors->has('asset_maintenance_type') ? ' has-error' : '' }}">
          <label for="asset_maintenance_type" class="col-md-3 control-label">
            {{ trans('admin/asset_maintenances/form.asset_maintenance_type') }}
          </label>
          <div class="col-md-7">
            <select class="form-control select2" name="asset_maintenance_type" id="asset_maintenance_type" style="width: 100%" {{ $item->id ? 'disabled' : '' }} required>
              <option value="">Pilih Jenis Maintenance</option>
              @foreach(\App\Models\StatusLabel::whereIn('name', ['IN Repair - IT', 'IN Repair - Vendor'])->get() as $status)
                <option value="{{ $status->name }}" {{ old('asset_maintenance_type', $item->asset_maintenance_type) == $status->name ? 'selected' : '' }}>
                  {{ $status->name }}
                </option>
              @endforeach
            </select>
            @if($item->id && $item->asset_maintenance_type)
                <!-- Hidden input to ensure value is submitted when field is disabled -->
                <input type="hidden" name="asset_maintenance_type" value="{{ $item->asset_maintenance_type }}">
            @endif
            {!! $errors->first('asset_maintenance_type', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>        <!-- Start Date -->
        <div class="form-group {{ $errors->has('start_date') ? ' has-error' : '' }}">
          <label for="start_date" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.start_date') }}</label>

          <div class="input-group col-md-3">
            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true" data-date-clear-btn="true">
              <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="start_date" id="start_date" value="{{ old('start_date', $item->start_date) }}"{{  (Helper::checkIfRequired($item, 'start_date')) ? ' required' : '' }}{{ $item->id ? ' disabled' : '' }}>
              <span class="input-group-addon"><x-icon type="calendar" /></span>
            </div>
            @if($item->id && $item->start_date)
                <!-- Hidden input to ensure value is submitted when field is disabled -->
                <input type="hidden" name="start_date" value="{{ $item->start_date }}">
            @endif
            {!! $errors->first('start_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

        @include('partials.event-checklist', ['checklistOptional' => true])

        <!-- Completion Date -->
        <div class="form-group {{ $errors->has('completion_date') ? ' has-error' : '' }}">
          <label for="start_date" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.completion_date') }}</label>

          <div class="input-group col-md-3">
            <div class="input-group date" data-date-clear-btn="true" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
              <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="completion_date" id="completion_date" value="{{ old('completion_date', $item->completion_date) }}"{{  (Helper::checkIfRequired($item, 'completion_date')) ? ' required' : '' }}>
              <span class="input-group-addon">
                <x-icon type="calendar" />
              </span>
            </div>
            {!! $errors->first('completion_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

          
          <!-- Completed -->
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
              <label class="form-control">
                <input type="checkbox" value="1" name="is_warranty" id="is_warranty" {{ old('is_warranty', $item->is_warranty) == '1' ? ' checked="checked"' : '' }}>
                Completed
              </label>
              <p class="help-block">Centang jika maintenance sudah selesai. Status asset akan berubah menjadi In-Checking.</p>
            </div>
          </div>

          @if($item->toolTransaction)
          <!-- Tool Transaction Info (read-only when editing) -->
          <div class="form-group">
            <label class="col-md-3 control-label">Tool Transaction</label>
            <div class="col-md-8">
              <p class="form-control-static">
                <strong>{{ $item->toolTransaction->document_number }}</strong>
                @if($item->toolTransaction->user)
                  - {{ $item->toolTransaction->user->present()->fullName() }}
                @endif
              </p>
            </div>
          </div>
          @endif        <!-- Asset Maintenance Cost -->
        <div class="form-group {{ $errors->has('cost') ? ' has-error' : '' }}">
          <label for="cost" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.cost') }}</label>
          <div class="col-md-2">
            <div class="input-group">
              <span class="input-group-addon">
                IDR
              </span>
              <input class="col-md-2 form-control" type="text" name="cost" id="cost" value="{{ old('cost', Helper::formatCurrencyOutput($item->cost)) }}" />
              {!! $errors->first('cost', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
          <label for="notes" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.notes') }}</label>
          <div class="col-md-7">
            <textarea class="col-md-6 form-control" id="notes" name="notes">{{ old('notes', $item->notes) }}</textarea>
            {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>
      </div> <!-- .box-body -->

      <div class="box-footer text-right">
        <button type="submit" class="btn btn-success"><x-icon type="checkmark" /> {{ trans('general.save') }}</button>
      </div>
    </div> <!-- .box-default -->
    </form>
  </div>
</div>

@stop

@section('moar_scripts')
<script>
$(document).ready(function() {
    // Handle tool transaction checkbox
    $('#create_tool_transaction').change(function() {
        if ($(this).is(':checked')) {
            $('#assigned_user_group').show();
            $('#assigned_user_id').prop('required', true);
        } else {
            $('#assigned_user_group').hide();
            $('#assigned_user_id').prop('required', false);
        }
    });

    // Initialize Select2
    $('.select2').select2();
});
</script>
@stop
