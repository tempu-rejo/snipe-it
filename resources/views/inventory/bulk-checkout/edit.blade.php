@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Edit Transaksi CheckOut: {{ $transaction->document_number }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                    Edit Transaksi CheckOut: {{ $transaction->document_number }}
                </h2>
                <div class="box-tools pull-right">
                    <a href="{{ route('inventory.bulk-checkout.show', $transaction->id) }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        Kembali ke Detail
                    </a>
                </div>
            </div>

            <form action="{{ route('inventory.bulk-checkout.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('user_id') ? 'has-error' : '' }}">
                                <label for="user_id">{{ trans('general.user') }} <span class="required">*</span></label>
                                <select name="user_id" class="form-control select2" style="width: 100%" required>
                                    <option value="">Pilih User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" 
                                            {{ old('user_id', $transaction->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->present()->fullName() }}
                                            @if($user->employee_num)
                                                ({{ $user->employee_num }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                {!! $errors->first('user_id', '<span class="help-block">:message</span>') !!}
                            </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('transaction_date') ? 'has-error' : '' }}">
                                    <label for="transaction_date">Tanggal Transaksi <span class="required">*</span></label>
                                    <input type="date"
                                           name="transaction_date"
                                           class="form-control"
                                           value="{{ old('transaction_date', $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') : '') }}"
                                           required>
                                    {!! $errors->first('transaction_date', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('on_behalf') ? 'has-error' : '' }}">
                                    <label for="on_behalf">On behalf</label>
                                    <input type="text" name="on_behalf" class="form-control"
                                           value="{{ old('on_behalf', $transaction->on_behalf) }}">
                                    {!! $errors->first('on_behalf', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('location_detail') ? 'has-error' : '' }}">
                                    <label for="location_detail">Loc. Detail</label>
                                    <input type="text" name="location_detail" class="form-control"
                                           value="{{ old('location_detail', $transaction->location_detail) }}">
                                    {!! $errors->first('location_detail', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('location_id') ? 'has-error' : '' }}">
                            <label for="location_id">{{ trans('general.location') }} / WH/Office</label>
                            <select name="location_id" class="form-control select2" style="width: 100%">
                                <option value="">Pilih WH/Office</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}"
                                        {{ old('location_id', $transaction->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('location_id', '<span class="help-block">:message</span>') !!}
                        </div>

                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label for="notes">{{ trans('general.notes') }}</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Catatan transaksi (opsional)">{{ old('notes', $transaction->notes) }}</textarea>
                            {!! $errors->first('notes', '<span class="help-block">:message</span>') !!}
                        </div>

                        <div class="info-box">
                            <div class="info-box-content">
                                <h4>Informasi Transaksi</h4>
                                <table class="table table-striped">
                                    <tr>
                                        <th width="40%">No. Dokumen:</th>
                                        <td>{{ $transaction->document_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td><span class="label label-primary">CheckOut</span></td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Asset:</th>
                                        <td>{{ $transaction->items->count() }} item</td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Oleh:</th>
                                        <td>{{ $transaction->createdBy?->present()->fullName() ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Dibuat:</th>
                                        <td>{{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    @include('partials.event-checklist')

                    <!-- Asset List -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Daftar Asset</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Asset Tag</th>
                                            <th>Asset Name</th>
                                            <th>Model</th>
                                            <th>Serial Number</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transaction->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->asset->asset_tag ?? '-' }}</td>
                                            <td>{{ $item->asset->name ?? '-' }}</td>
                                            <td>{{ $item->asset->model->name ?? '-' }}</td>
                                            <td>{{ $item->asset->serial ?? '-' }}</td>
                                            <td>
                                                @if($item->asset->status_label)
                                                    <span class="label" style="background-color: {{ $item->asset->status_label->color }};">
                                                        {{ $item->asset->status_label->name }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        Update Transaksi
                    </button>
                    <a href="{{ route('inventory.bulk-checkout.show', $transaction->id) }}" class="btn btn-default">
                        {{ trans('button.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('moar_scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>
@stop
