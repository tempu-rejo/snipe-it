<?php
use Carbon\Carbon;
?>
@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/asset_maintenances/general.view') }} {{ $assetMaintenance->title }}
@parent
@stop

@section('header_right')
  <div class="pull-right">
    <a href="{{ route('maintenances.print', $assetMaintenance) }}" class="btn btn-info pull-right" target="_BLANK">
       <i class="fas fa-print"></i>{{ trans('general.print') }}
    </a>
    
    <a href="{{ route('maintenances.edit', $assetMaintenance) }}" class="btn btn-default pull-right" style="margin-right: 10px;">
      {{ trans('general.update') }}</a>

    <a href="{{ route('maintenances.index') }}" class="btn btn-primary text-right" style="margin-right: 10px;">{{ trans('general.back') }}</a>
  </div>
@stop


{{-- Page content --}}
@section('content')
  <div class="row">
    <div class="col-md-9">

      <div class="box box-default">
        <div class="box-body">
          <div class="row-new-striped">
            <div class="row">

                <div class="col-md-3">
                  {{ trans('admin/asset_maintenances/form.asset_maintenance_type') }}
                </div>
                <div class="col-md-9">
                  {{ $assetMaintenance->asset_maintenance_type }}
                </div>

            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('general.asset') }}
              </div>
              <div class="col-md-9">
                <a href="{{ route('hardware.show', $assetMaintenance->asset_id) }}">
                  {{ $assetMaintenance->asset->present()->fullName }}
                </a>
              </div>
            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('general.category') }}
              </div>
              <div class="col-md-9">
                {{ optional($assetMaintenance->asset->model->category)->name ?? '-' }}
              </div>
            </div> <!-- /row -->
            <div class="row">
              <div class="col-md-3">
                {{ trans('general.manufacturer') }}
              </div>
              <div class="col-md-9">
                {{ optional($assetMaintenance->asset->model->manufacturer)->name ?? '-' }}
              </div>
            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('general.supplier') }}
              </div>
              <div class="col-md-9">
                <a href="{{ route('suppliers.show', $assetMaintenance->supplier_id) }}">
                  {{ $assetMaintenance->supplier->name }}
                </a>
              </div>
            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('general.location') }}
              </div>
              <div class="col-md-9">
                @if ($assetMaintenance->asset->defaultLoc)
                  <a href="{{ route('locations.show', $assetMaintenance->asset->rtd_location_id) }}">
                    {{ $assetMaintenance->asset->defaultLoc->name }}
                  </a>
                @elseif ($assetMaintenance->asset->location)
                  <a href="{{ route('locations.show', $assetMaintenance->asset->location_id) }}">
                    {{ $assetMaintenance->asset->location->name }}
                  </a>
                @else
                  -
                @endif
              </div>
            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('admin/asset_maintenances/form.start_date') }}
              </div>
              <div class="col-md-9">
                {{ Helper::getFormattedDateObject($assetMaintenance->start_date, 'date', false) }}
              </div>
            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('admin/asset_maintenances/form.completion_date') }}
              </div>
              <div class="col-md-9">
                @if ($assetMaintenance->completion_date)
                  {{ Helper::getFormattedDateObject($assetMaintenance->completion_date, 'date', false) }}
                @else
                  {{ trans('admin/asset_maintenances/message.asset_maintenance_incomplete') }}
                @endif
              </div>
            </div> <!-- /row -->

            <div class="row">
              <div class="col-md-3">
                {{ trans('admin/asset_maintenances/form.asset_maintenance_time') }}
              </div>
              <div class="col-md-9">
                {{ $assetMaintenance->asset_maintenance_time }}
              </div>
            </div> <!-- /row -->

            @if ($assetMaintenance->cost > 0)
            <div class="row">
              <div class="col-md-3">
                {{ trans('admin/asset_maintenances/form.cost') }}
              </div>
              <div class="col-md-9">
                {{ trans( 'general.currency' ) . Helper::formatCurrencyOutput($assetMaintenance->cost) }}
              </div>
            </div> <!-- /row -->
            @endif

            <div class="row">
              <div class="col-md-3">
                {{ trans('admin/asset_maintenances/form.is_warranty') }}
              </div>
              <div class="col-md-9">
                {{ $assetMaintenance->is_warranty ? trans('admin/asset_maintenances/message.warranty') : trans('admin/asset_maintenances/message.not_warranty') }}
              </div>
            </div> <!-- /row -->

            @if ($assetMaintenance->notes)
            <div class="row">
              <div class="col-md-3">
                {{ trans('admin/asset_maintenances/form.notes') }}
              </div>
              <div class="col-md-9">
                {!! nl2br(Helper::parseEscapedMarkedownInline($assetMaintenance->notes)) !!}
              </div>
            </div> <!-- /row -->
            @endif

              @if ($checklistSubmissions->isNotEmpty())
                @foreach ($checklistSubmissions as $submission)
                  <div class="row">
                    <div class="col-md-12">
                      <h4>{{ $submission->checklist?->name ?? 'Event Checklist' }}</h4>
                      <p class="text-muted">
                        {{ $submission->created_at?->format('Y-m-d H:i') }}
                        @if ($submission->submitter)
                          &mdash; {{ $submission->submitter->present()->fullName() }}
                        @endif
                      </p>
                      @php
                        $submissionColumns = $submission->checklist?->columns ?? [];
                        $submissionRows = $submission->rows ?? [];
                      @endphp
                      <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              @if ($submissionColumns === [])
                                <th>No</th>
                                <th>Checkpoint</th>
                                <th>Result</th>
                                <th>Evidence</th>
                              @else
                                @foreach ($submissionColumns as $column)
                                  <th>{{ $column['label'] ?? $column['key'] }}</th>
                                @endforeach
                                @if (!collect($submissionColumns)->contains(fn ($column) => ($column['key'] ?? null) === 'evidence'))
                                  <th>Evidence</th>
                                @endif
                              @endif
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($submissionRows as $rowIndex => $row)
                              <tr>
                                @if ($submissionColumns === [])
                                  <td>{{ $rowIndex + 1 }}</td>
                                  <td>{{ $row['checkpoint'] ?? '-' }}</td>
                                  <td>{{ $row['result'] ?? '-' }}</td>
                                  <td>
                                    @if (!empty($row['evidence_file']['path']))
                                      <a href="{{ route('event-checklist-submissions.evidence', [$submission->id, $rowIndex]) }}">
                                        {{ $row['evidence_file']['name'] ?? 'Download evidence' }}
                                      </a>
                                    @else
                                      {{ $row['evidence'] ?? '-' }}
                                    @endif
                                  </td>
                                @else
                                  @foreach ($submissionColumns as $column)
                                    @php
                                      $columnKey = $column['key'] ?? '';
                                      $cellValue = $row[$columnKey] ?? '-';
                                      if (is_bool($cellValue)) {
                                          $cellValue = $cellValue ? 'Yes' : 'No';
                                      }
                                    @endphp
                                    <td>{{ is_array($cellValue) ? implode(', ', $cellValue) : $cellValue }}</td>
                                  @endforeach
                                  @if (!collect($submissionColumns)->contains(fn ($column) => ($column['key'] ?? null) === 'evidence'))
                                    <td>
                                      @if (!empty($row['evidence_file']['path']))
                                        <a href="{{ route('event-checklist-submissions.evidence', [$submission->id, $rowIndex]) }}">
                                          {{ $row['evidence_file']['name'] ?? 'Download evidence' }}
                                        </a>
                                      @else
                                        {{ $row['evidence'] ?? '-' }}
                                      @endif
                                    </td>
                                  @endif
                                @endif
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                      @if ($submission->evidence_files !== [])
                        <p>
                          <strong>Attachment:</strong>
                          @foreach ($submission->evidence_files as $fileIndex => $evidenceFile)
                            <a href="{{ route('event-checklist-submissions.evidence', [$submission->id, 'header-'.$fileIndex]) }}">
                              {{ $evidenceFile['name'] ?? 'Download evidence' }}
                            </a>@if (!$loop->last), @endif
                          @endforeach
                        </p>
                      @endif
                    </div>
                  </div>
                @endforeach
              @endif

              </div><!-- /row-new-striped -->
      </div><!-- /box-body -->
    </div><!-- /box -->

    </div> <!-- col-md-9  end -->
  </div> <!-- row  end -->

@stop
