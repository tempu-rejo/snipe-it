@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Buat Transaksi Bulk CheckOut
    @parent
@stop

{{-- Page content --}}
@section('content')

<style>
    .asset-row {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
        padding: 15px;
        background-color: #f9f9f9;
    }
    .asset-row .remove-asset {
        margin-top: 5px;
    }
    .transaction-create-form {
        width: 100%;
    }
    .asset-details .form-control-static {
        overflow-wrap: anywhere;
    }
    .asset-details-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        align-items: start;
        gap: 15px;
    }
    .asset-details-grid label {
        display: block;
    }
    @media (max-width: 991px) {
        .asset-details-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 600px) {
        .asset-details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row">
    <div class="col-md-12 transaction-create-form">
        <form class="form-horizontal" method="post" action="{{ route('inventory.bulk-checkout.store') }}" autocomplete="off" enctype="multipart/form-data">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h2 class="box-title">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        Buat Transaksi Bulk CheckOut
                    </h2>
                </div>

                <div class="box-body">
                    {{ csrf_field() }}

                    <!-- Document Number (Read Only) -->
                    <div class="form-group">
                        <label for="document_number" class="col-md-2 control-label">
                            {{ trans('general.document_number') }}
                        </label>
                        <div class="col-md-10">
                            <p class="form-control-static">
                                <strong>{{ $documentNumber }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('default_location') ? 'has-error' : '' }}">
                        <label for="default_location" class="col-md-2 control-label">WH/Office</label>
                        <div class="col-md-10">
                            <select class="form-control select2" name="default_location" id="default_location" style="width: 100%">
                                <option value="">Pilih WH/Office</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('default_location', $preSelectedAsset?->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('default_location'))
                                <span class="help-block">{{ $errors->first('default_location') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('user_id') ? 'has-error' : '' }}">
                                <label for="user_id" class="col-md-4 control-label">
                                    {{ trans('general.user') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <select class="form-control select2" name="user_id" id="user_id" style="width: 100%" required>
                                        <option value="">{{ trans('general.select_user') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->present()->fullName() }}
                                                @if($user->employee_num)
                                                    ({{ $user->employee_num }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('user_id'))
                                        <span class="help-block">{{ $errors->first('user_id') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('transaction_date') ? 'has-error' : '' }}">
                                <label for="transaction_date" class="col-md-4 control-label">
                                    {{ trans('general.date') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                                        <input type="text" class="form-control" name="transaction_date" id="transaction_date"
                                               value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                        <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    @if($errors->has('transaction_date'))
                                        <span class="help-block">{{ $errors->first('transaction_date') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('on_behalf') ? 'has-error' : '' }}">
                                <label for="on_behalf" class="col-md-4 control-label">On behalf</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="on_behalf" id="on_behalf" value="{{ old('on_behalf') }}">
                                    <span class="help-block">Nilai ini juga memperbarui On Behalf pada setiap aset yang berhasil di-check-out.</span>
                                    @if($errors->has('on_behalf'))
                                        <span class="help-block">{{ $errors->first('on_behalf') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('location_detail') ? 'has-error' : '' }}">
                                <label for="location_detail" class="col-md-4 control-label">Loc. Detail</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="location_detail" id="location_detail" value="{{ old('location_detail') }}">
                                    <span class="help-block">Nilai ini juga memperbarui Loc. Detail pada setiap aset yang berhasil di-check-out.</span>
                                    @if($errors->has('location_detail'))
                                        <span class="help-block">{{ $errors->first('location_detail') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                        <label for="notes" class="col-md-2 control-label">
                            {{ trans('general.notes') }}
                        </label>
                        <div class="col-md-10">
                            <textarea class="form-control" name="notes" id="notes" rows="3">{{ old('notes') }}</textarea>
                            <span class="help-block">Catatan transaksi ini juga memperbarui Notes pada setiap aset yang berhasil di-check-out.</span>
                            @if($errors->has('notes'))
                                <span class="help-block">{{ $errors->first('notes') }}</span>
                            @endif
                        </div>
                    </div>

                    @include('partials.event-checklist')

                    <hr>

                    <!-- Assets Section -->
                    <div class="form-group">
                        <label class="col-md-2 control-label">
                            Assets <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-10">
                            <button type="button" class="btn btn-success" id="add-asset">
                                <i class="fas fa-plus"></i> Tambah Asset
                            </button>
                        </div>
                    </div>

                    <div id="assets-container">
                        <!-- Asset rows will be added here -->
                    </div>

                    @if($errors->has('assets'))
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-2">
                                <span class="help-block text-danger">{{ $errors->first('assets') }}</span>
                            </div>
                        </div>
                    @endif

                </div> <!-- /.box-body -->

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-2">
                            <a class="btn btn-link" href="{{ route('inventory.bulk-checkout.index') }}">
                                {{ trans('button.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check icon-white" aria-hidden="true"></i>
                                {{ trans('general.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('moar_scripts')
<script>
    // Pass PHP data to JavaScript
    window.preSelectedAsset = @json($preSelectedAsset ?? null);
    const assetDetailsUrlTemplate = @json(route('hardware.asset-details', ['tag' => '__asset_tag__']));
</script>

<script>
    let assetIndex = 0;

    function addAssetRow() {
        const container = document.getElementById('assets-container');
        const assetRow = document.createElement('div');
        assetRow.className = 'asset-row';
        assetRow.id = 'asset-row-' + assetIndex;
        
        assetRow.innerHTML = `
            <div class="asset-details asset-details-grid">
                <div>
                    <label>Asset Tag <span class="text-danger">*</span></label>
                    <input type="text" class="form-control asset-tag-input" name="assets[${assetIndex}][asset_tag]"
                           placeholder="Scan atau ketik asset tag" required>
                </div>
                <div>
                    <label>Asset Name</label>
                    <div class="form-control-static asset-name">-</div>
                </div>
                <div>
                    <label>Model</label>
                    <div class="form-control-static asset-model">-</div>
                </div>
                <div>
                    <label>Serial Number</label>
                    <div class="form-control-static asset-serial">-</div>
                </div>
                <div>
                    <label>Notes</label>
                    <input type="text" class="form-control" name="assets[${assetIndex}][notes]"
                           placeholder="Catatan untuk asset ini">
                </div>
            </div>
            <div class="asset-lookup-error text-danger" style="display: none; margin-top: 8px;"></div>
            <div class="row">
                <div class="col-md-12" style="margin-top: 10px;">
                    <button type="button" class="btn btn-danger btn-sm remove-asset" onclick="removeAssetRow(${assetIndex})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(assetRow);
        assetIndex++;

        // Focus on the new asset tag input
        const newInput = assetRow.querySelector('.asset-tag-input');
        newInput.focus();
    }

    function removeAssetRow(index) {
        const row = document.getElementById('asset-row-' + index);
        if (row) {
            row.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 first
        $('.select2').select2();

        // Pre-fill asset if coming from single checkout
        const preSelectedAsset = window.preSelectedAsset;
        
        if (preSelectedAsset) {
            addAssetRowWithData({
                asset_tag: preSelectedAsset.asset_tag,
                name: preSelectedAsset.name || 'N/A',
                model: preSelectedAsset.model ? preSelectedAsset.model.name : 'N/A',
                serial: preSelectedAsset.serial || 'N/A'
            });
            
            // Pre-fill default location from asset's current location
            if (preSelectedAsset.location && preSelectedAsset.location.id) {
                $('#default_location').val(preSelectedAsset.location.id).trigger('change');
            }
        } else {
            // Add first empty asset row
            addAssetRow();
        }

        // Add asset button click
        document.getElementById('add-asset').addEventListener('click', addAssetRow);

        // Resolve each scanned tag before moving on to the next asset.
        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('asset-tag-input') && e.key === 'Enter') {
                e.preventDefault();
                fetchAssetDetails(e.target);
                addAssetRow();
            }
        });

        document.addEventListener('focusout', function(e) {
            if (e.target.classList.contains('asset-tag-input')) {
                fetchAssetDetails(e.target);
            }
        });
    });

    function addAssetRowWithData(assetData) {
        const container = document.getElementById('assets-container');
        const assetRow = document.createElement('div');
        assetRow.className = 'asset-row';
        assetRow.id = 'asset-row-' + assetIndex;
        
        assetRow.innerHTML = `
            <div class="asset-details asset-details-grid">
                <div>
                    <label>Asset Tag <span class="text-danger">*</span></label>
                    <input type="text" class="form-control asset-tag-input" name="assets[${assetIndex}][asset_tag]"
                           placeholder="Scan atau ketik asset tag" required>
                </div>
                <div>
                    <label>Asset Name</label>
                    <div class="form-control-static asset-name"></div>
                </div>
                <div>
                    <label>Model</label>
                    <div class="form-control-static asset-model"></div>
                </div>
                <div>
                    <label>Serial Number</label>
                    <div class="form-control-static asset-serial"></div>
                </div>
                <div>
                    <label>Notes</label>
                    <input type="text" class="form-control" name="assets[${assetIndex}][notes]"
                           placeholder="Catatan untuk asset ini">
                </div>
            </div>
            <div class="asset-lookup-error text-danger" style="display: none; margin-top: 8px;"></div>
            <div class="row">
                <div class="col-md-12" style="margin-top: 10px;">
                    <button type="button" class="btn btn-danger btn-sm remove-asset" onclick="removeAssetRow(${assetIndex})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(assetRow);
        assetRow.querySelector('.asset-tag-input').value = assetData.asset_tag;
        assetRow.querySelector('.asset-name').textContent = assetData.name;
        assetRow.querySelector('.asset-model').textContent = assetData.model;
        assetRow.querySelector('.asset-serial').textContent = assetData.serial;
        assetIndex++;
    }

    function fetchAssetDetails(inputElement) {
        const assetTag = inputElement.value.trim();
        const assetRow = inputElement.closest('.asset-row');
        const detailsDiv = assetRow.querySelector('.asset-details');
        const errorDiv = assetRow.querySelector('.asset-lookup-error');

        if (!assetTag) {
            detailsDiv.style.display = 'grid';
            errorDiv.style.display = 'none';
            assetRow.dataset.lookupTag = '';
            return Promise.resolve();
        }

        if (assetRow.dataset.lookupTag === assetTag) {
            return assetRow.lookupPromise || Promise.resolve();
        }

        assetRow.lookupController?.abort();
        const controller = new AbortController();
        assetRow.lookupController = controller;
        assetRow.dataset.lookupTag = assetTag;
        detailsDiv.style.display = 'grid';
        assetRow.querySelector('.asset-name').textContent = '-';
        assetRow.querySelector('.asset-model').textContent = '-';
        assetRow.querySelector('.asset-serial').textContent = '-';
        errorDiv.style.display = 'none';

        assetRow.lookupPromise = fetch(
            assetDetailsUrlTemplate.replace('__asset_tag__', encodeURIComponent(assetTag)),
            { headers: { 'Accept': 'application/json' }, signal: controller.signal }
        )
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.status === 404 ? 'Asset tag tidak ditemukan.' : 'Detail asset tidak dapat dimuat.');
                }
                return response.json();
            })
            .then(data => {
                if (inputElement.value.trim() !== assetTag) {
                    return;
                }
                assetRow.querySelector('.asset-name').textContent = data.name || 'N/A';
                assetRow.querySelector('.asset-model').textContent = data.model?.name || 'N/A';
                assetRow.querySelector('.asset-serial').textContent = data.serial || 'N/A';
                detailsDiv.style.display = 'grid';
            })
            .catch(error => {
                if (error.name === 'AbortError') {
                    return;
                }
                assetRow.dataset.lookupTag = '';
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
                console.error('Error fetching asset details:', error);
            });

        return assetRow.lookupPromise;
    }
</script>
@stop
