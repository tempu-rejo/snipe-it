@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Detail Transaksi CheckOut: {{ $transaction->document_number }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    Detail Transaksi CheckOut: {{ $transaction->document_number }}
                </h2>
                <div class="box-tools pull-right">
                    <a href="{{ route('inventory.bulk-checkout.edit', $transaction->id) }}" 
                       class="btn btn-warning btn-sm" 
                       style="margin-right: 5px;">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                        Edit
                    </a>
                    <a href="{{ route('inventory.bulk-checkout.print', $transaction->id) }}" 
                       class="btn btn-primary btn-sm" 
                       target="_blank" 
                       style="margin-right: 5px;">
                        <i class="fas fa-print" aria-hidden="true"></i>
                        Print
                    </a>
                    <a href="{{ route('inventory.bulk-checkout.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <!-- Transaction Info -->
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">No. Dokumen</th>
                                <td>{{ $transaction->document_number }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Transaksi</th>
                                <td>{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>User</th>
                                <td>
                                    @if($transaction->user)
                                        {{ $transaction->user->present()->fullName() }}
                                        @if($transaction->user->employee_num)
                                            <br><small class="text-muted">{{ $transaction->user->employee_num }}</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>WH/Office</th>
                                <td>
                                    @if($transaction->location)
                                        {{ $transaction->location->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Loc. Detail</th>
                                <td>{{ $transaction->location_detail ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>On behalf</th>
                                <td>{{ $transaction->on_behalf ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>
                                    @if($transaction->createdBy)
                                        {{ $transaction->createdBy->present()->fullName() }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Notes -->
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Catatan</th>
                                <td>
                                    @if($transaction->notes)
                                        {{ $transaction->notes }}
                                    @else
                                        <em class="text-muted">Tidak ada catatan</em>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $checklistItem = $transaction->items->first(fn ($item) => $item->checklistSubmission !== null);
                                $checklistSubmission = $checklistItem?->checklistSubmission;
                            @endphp
                            @if ($checklistSubmission)
                                <tr>
                                    <th>Event Checklist</th>
                                    <td>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-condensed">
                                                <thead>
                                                    <tr>
                                                        <th>Checkpoint</th>
                                                        <th>Result</th>
                                                        <th>Evidence</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($checklistSubmission->evidence_files !== [])
                                                        <tr>
                                                            <td colspan="3">
                                                                <strong>Checklist attachment:</strong>
                                                                @foreach ($checklistSubmission->evidence_files as $fileIndex => $evidenceFile)
                                                                    <a href="{{ route('event-checklist-submissions.evidence', [$checklistSubmission->id, 'header-'.$fileIndex]) }}">
                                                                        <i class="fas fa-paperclip" aria-hidden="true"></i>
                                                                        {{ $evidenceFile['name'] ?? 'Checklist evidence file' }}
                                                                    </a>@if (!$loop->last), @endif
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @foreach ($checklistSubmission->rows ?? [] as $rowIndex => $row)
                                                        <tr>
                                                            <td>{{ $row['checkpoint'] ?? '-' }}</td>
                                                            <td>{{ $row['result'] ?? '-' }}</td>
                                                            <td>
                                                                @if (!empty($row['evidence']))
                                                                    <div>{{ $row['evidence'] }}</div>
                                                                @endif
                                                                @if (!empty($row['evidence_file']['path']))
                                                                    <a href="{{ route('event-checklist-submissions.evidence', [$checklistSubmission->id, $rowIndex]) }}">
                                                                        <i class="fas fa-paperclip" aria-hidden="true"></i>
                                                                        {{ $row['evidence_file']['name'] ?? 'Download evidence' }}
                                                                    </a>
                                                                @endif
                                                                @if (empty($row['evidence']) && empty($row['evidence_file']['path']))
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Assets List -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Assets yang di-Check-Out ({{ $transaction->items->count() }} items)</h4>

                        @if($transaction->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Asset Tag</th>
                                            <th>Nama Asset</th>
                                            <th>Model</th>
                                            <th>Serial Number</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transaction->items as $item)
                                            <tr>
                                                <td>
                                                    @if($item->asset)
                                                        <a href="{{ route('hardware.show', $item->asset->id) }}">
                                                            {{ $item->asset->asset_tag }}
                                                        </a>
                                                    @else
                                                        <span class="text-danger">Asset tidak ditemukan</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->asset)
                                                        {{ $item->asset->name ?: '-' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->asset && $item->asset->model)
                                                        {{ $item->asset->model->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->asset)
                                                        {{ $item->asset->serial ?: '-' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->asset && $item->asset->assetstatus)
                                                        <span class="label label-default">
                                                            {{ $item->asset->assetstatus->name }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->notes)
                                                        {{ $item->notes }}
                                                    @else
                                                        <em class="text-muted">-</em>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Tidak ada asset dalam transaksi ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
