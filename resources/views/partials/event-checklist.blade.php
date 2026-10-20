@php
    $statusChecklistMode = $statusChecklistMode ?? false;
    $checklistOptional = $checklistOptional ?? false;
    $eventChecklists = $eventChecklists ?? collect();
    $existingChecklistSubmission = $eventChecklistSubmission ?? null;
    $checklistHasErrors = collect($errors->keys())->contains(
        fn ($key) => str_starts_with($key, 'event_checklist')
    );
@endphp
@if ($statusChecklistMode || $eventChecklists->isNotEmpty())
    <div
        class="form-group event-checklist-form-group"
        @if ($statusChecklistMode)
            id="status-event-checklist"
            data-status-checklist-mode="true"
            data-from-status-id="{{ $checklistFromStatusId ?? '' }}"
            data-existing-checklist-id="{{ $existingChecklistSubmission?->event_checklist_id ?? '' }}"
            data-expanded-on-load="{{ $checklistHasErrors ? 'true' : 'false' }}"
            hidden
        @endif
    >
        @if ($statusChecklistMode)
            <input type="hidden" id="event_checklist_submission_id" name="event_checklist_submission_id" value="">
        @endif
        <label class="col-sm-2 control-label">Checklist</label>
        <div class="col-md-10">
            @if ($statusChecklistMode && $errors->has('event_checklist'))
                <div class="alert alert-danger">{{ $errors->first('event_checklist') }}</div>
            @endif
            @if ($statusChecklistMode)
                <div class="alert alert-warning checklist-no-match" hidden>
                    No checklist is configured for the selected status and asset category. Configure a checklist before saving this hardware.
                </div>
            @endif
            @foreach ($eventChecklists as $checklist)
                @php
                    $fromStatusName = $checklist->fromStatus?->name ?? 'Any';
                    $toStatusName = $checklist->toStatus?->name ?? 'Unknown status';
                @endphp
                <div
                    class="panel panel-default event-checklist"
                    data-checklist-id="{{ $checklist->id }}"
                    data-target-status="{{ $toStatusName }}"
                    data-target-status-id="{{ $checklist->to_status_id }}"
                    data-category-id="{{ $checklist->category_id ?? '' }}"
                    data-from-status-id="{{ $checklist->from_status_id ?? '' }}"
                    @if ($statusChecklistMode) hidden @endif
                >
                    <div class="panel-heading checklist-header">
                        <strong>{{ $checklist->name }} ({{ $fromStatusName }} &rarr; {{ $toStatusName }})</strong>
                        <label class="checklist-header-upload">
                            Evidence files (multiple files allowed; also saved to asset Files)
                            <input class="checklist-evidence-file" type="file" name="event_checklist_files[{{ $checklist->id }}][]" multiple data-maxsize="{{ Helper::file_upload_max_size() }}" accept=".png,.gif,.jpg,.svg,.jpeg,.doc,.docx,.pdf,.txt,.zip,.rar,.xls,.xlsx,.lic,.xml,.rtf,.json,.webp,.avif" @disabled($statusChecklistMode)>
                        </label>
                        <span class="help-block">{{ trans('general.upload_filetypes_help', ['size' => Helper::file_upload_max_size_readable()]) }}</span>
                        @if (!empty($eventChecklistSubmission) && (int) $eventChecklistSubmission->event_checklist_id === (int) $checklist->id)
                            @foreach ($eventChecklistSubmission->evidence_files as $fileIndex => $evidenceFile)
                                <a href="{{ route('event-checklist-submissions.evidence', [$eventChecklistSubmission->id, 'header-'.$fileIndex]) }}">
                                    <i class="fas fa-paperclip" aria-hidden="true"></i>
                                    {{ $evidenceFile['name'] ?? 'Current evidence file' }}
                                </a>
                            @endforeach
                        @endif
                        @foreach ($errors->messages() as $errorKey => $messages)
                            @if (str_starts_with($errorKey, 'event_checklist.file'))
                                @foreach ($messages as $message)
                                    <span class="text-danger">{{ $message }}</span>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                    <div class="panel-body">
                        @if ($errors->has('event_checklist'))
                            <div class="alert alert-danger">{{ $errors->first('event_checklist') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-condensed checklist-rows">
                                <thead><tr>@foreach ($checklist->columns ?? [] as $column)<th class="checklist-column-{{ $column['key'] }}">{{ $column['label'] }} @if(!empty($column['required']))* @endif</th>@endforeach</tr></thead>
                                <tbody>
                                @foreach (($checklist->rows ?: [[]]) as $rowIndex => $templateRow)
                                    <tr>
                                        @foreach ($checklist->columns ?? [] as $column)
                                            @php
                                                $key = $column['key'];
                                                $value = $templateRow[$key] ?? null;
                                                $isTemplateValue = $key !== 'result' && $value !== null && $value !== '';
                                            @endphp
                                            <td class="checklist-column-{{ $key }}">
                                                @if ($isTemplateValue)
                                                    <input class="form-control checklist-template-value" type="text" value="{{ $value }}" readonly>
                                                    <input type="hidden" name="event_checklist_rows[{{ $checklist->id }}][{{ $rowIndex }}][{{ $key }}]" value="{{ $value }}" @disabled($statusChecklistMode)>
                                                @elseif (($column['type'] ?? 'text') === 'textarea')
                                                    <textarea class="form-control" name="event_checklist_rows[{{ $checklist->id }}][{{ $rowIndex }}][{{ $key }}]" @required(!$checklistOptional && (!empty($column['required']) || $key === 'result')) @disabled($statusChecklistMode)>{{ old("event_checklist_rows.{$checklist->id}.{$rowIndex}.{$key}", data_get($eventChecklistValues ?? [], "{$checklist->id}.{$rowIndex}.{$key}")) }}</textarea>
                                                @elseif (($column['type'] ?? 'text') === 'select')
                                                    <select class="form-control" name="event_checklist_rows[{{ $checklist->id }}][{{ $rowIndex }}][{{ $key }}]" @required(!$checklistOptional && (!empty($column['required']) || $key === 'result')) @disabled($statusChecklistMode)>
                                                        <option value="">Select</option>
                                                        @foreach (($column['options'] ?? []) as $option)<option value="{{ $option }}" @selected(old("event_checklist_rows.{$checklist->id}.{$rowIndex}.{$key}", data_get($eventChecklistValues ?? [], "{$checklist->id}.{$rowIndex}.{$key}")) == $option)>{{ $option }}</option>@endforeach
                                                    </select>
                                                @elseif (($column['type'] ?? 'text') === 'checkbox')
                                                    <input type="checkbox" name="event_checklist_rows[{{ $checklist->id }}][{{ $rowIndex }}][{{ $key }}]" value="1" @checked(old("event_checklist_rows.{$checklist->id}.{$rowIndex}.{$key}", data_get($eventChecklistValues ?? [], "{$checklist->id}.{$rowIndex}.{$key}"))) @required(!$checklistOptional && !empty($column['required'])) @disabled($statusChecklistMode)>
                                                @else
                                                    <input class="form-control" type="{{ in_array($column['type'] ?? 'text', ['number', 'date']) ? $column['type'] : 'text' }}" name="event_checklist_rows[{{ $checklist->id }}][{{ $rowIndex }}][{{ $key }}]" value="{{ old("event_checklist_rows.{$checklist->id}.{$rowIndex}.{$key}", data_get($eventChecklistValues ?? [], "{$checklist->id}.{$rowIndex}.{$key}")) }}" @required(!$checklistOptional && (!empty($column['required']) || $key === 'result')) @disabled($statusChecklistMode)>
                                                @endif
                                                @if ($errors->has("event_checklist.rows.{$rowIndex}.{$key}"))
                                                    <span class="text-danger">{{ $errors->first("event_checklist.rows.{$rowIndex}.{$key}") }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if (empty($checklist->rows))
                            <button type="button" class="btn btn-default btn-xs add-checklist-row">Add row</button>
                        @endif
                    </div>
                </div>
            @endforeach
            @if (!$statusChecklistMode)
                @if ($checklistOptional)
                    <p class="help-block">Checklist is optional. Select a maintenance type to display and optionally complete its checklist.</p>
                @else
                    <p class="help-block">Complete only the checklist that belongs to the selected status transition.</p>
                @endif
            @endif
        </div>
    </div>
    <style>
        .event-checklist-form-group .event-checklist {
            width: 100%;
        }

        .event-checklist .checklist-column-no {
            width: 3ch;
            min-width: 3ch;
            max-width: 3ch;
        }

        .event-checklist .checklist-column-no input {
            width: 3ch;
            padding-left: 2px;
            padding-right: 2px;
        }

        .event-checklist .checklist-column-area {
            width: 15ch;
            min-width: 15ch;
            max-width: 15ch;
        }

        .event-checklist .checklist-column-area input {
            width: 15ch;
        }

        .event-checklist .checklist-column-checkpoint {
            width: 50ch;
            min-width: 50ch;
            max-width: 50ch;
        }

        .event-checklist .checklist-column-checkpoint input {
            width: 50ch;
            max-width: none;
        }

        .event-checklist .checklist-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .event-checklist .checklist-header-upload {
            margin: 0;
        }

        .event-checklist .checklist-header .help-block {
            margin: 0;
        }

        .event-checklist .checklist-template-value {
            min-width: 240px;
        }
    </style>
    <script>
        document.querySelectorAll('.add-checklist-row').forEach(function (button) {
            button.addEventListener('click', function () {
                var table = button.closest('.panel-body').querySelector('tbody');
                var row = table.querySelector('tr').cloneNode(true);
                var index = table.querySelectorAll('tr').length;
                row.querySelectorAll('[name]').forEach(function (field) {
                    if (field.type !== 'hidden') {
                        field.value = '';
                    }
                    field.checked = false;
                    field.name = field.name.replace(/(\[\d+\])\[\d+\](?=\[|$)/, '$1[' + index + ']');
                });
                table.appendChild(row);
            });
        });

        var statusChecklist = document.getElementById('status-event-checklist');
        if (statusChecklist) {
            var statusSelect = document.getElementById('status_select_id');
            var modelSelect = document.getElementById('model_select_id');
            var noChecklistMessage = statusChecklist.querySelector('.checklist-no-match');
            var existingSubmissionInput = document.getElementById('event_checklist_submission_id');
            var activeChecklist = null;
            var checklistExpanded = statusChecklist.dataset.expandedOnLoad === 'true';

            function selectedCategoryId() {
                if (!modelSelect || !modelSelect.value) {
                    return '';
                }

                var selectedOption = modelSelect.options[modelSelect.selectedIndex];
                return selectedOption?.dataset.categoryId
                    || modelSelect.dataset.selectedCategoryId
                    || '';
            }

            function updateStatusChecklist() {
                var statusId = statusSelect?.value || '';
                var categoryId = selectedCategoryId();
                var fromStatusId = statusChecklist.dataset.fromStatusId || '';
                var sameStatus = statusId === fromStatusId;
                var existingChecklistId = statusChecklist.dataset.existingChecklistId || '';
                var matchingChecklists = Array.from(statusChecklist.querySelectorAll('.event-checklist'))
                    .filter(function (panel) {
                        if (sameStatus) {
                            return existingChecklistId !== ''
                                && panel.dataset.checklistId === existingChecklistId;
                        }

                        return panel.dataset.targetStatusId === statusId
                            && (panel.dataset.categoryId === categoryId || panel.dataset.categoryId === '')
                            && (panel.dataset.fromStatusId === fromStatusId || panel.dataset.fromStatusId === '');
                    })
                    .sort(function (first, second) {
                        var firstCategoryRank = first.dataset.categoryId === categoryId ? 0 : 1;
                        var secondCategoryRank = second.dataset.categoryId === categoryId ? 0 : 1;
                        if (firstCategoryRank !== secondCategoryRank) {
                            return firstCategoryRank - secondCategoryRank;
                        }

                        var firstFromStatusRank = first.dataset.fromStatusId === fromStatusId ? 0 : 1;
                        var secondFromStatusRank = second.dataset.fromStatusId === fromStatusId ? 0 : 1;
                        if (firstFromStatusRank !== secondFromStatusRank) {
                            return firstFromStatusRank - secondFromStatusRank;
                        }

                        return Number(first.dataset.checklistId) - Number(second.dataset.checklistId);
                    });

                statusChecklist.querySelectorAll('.event-checklist').forEach(function (panel) {
                    panel.hidden = true;
                    panel.querySelectorAll('input, select, textarea').forEach(function (field) {
                        field.disabled = true;
                    });
                });

                activeChecklist = matchingChecklists[0] || null;
                if (existingSubmissionInput) {
                    existingSubmissionInput.value = checklistExpanded
                        && sameStatus
                        && activeChecklist
                        && activeChecklist.dataset.checklistId === existingChecklistId
                        ? @json($existingChecklistSubmission?->id)
                        : '';
                }
                if (activeChecklist && checklistExpanded) {
                    activeChecklist.hidden = false;
                    activeChecklist.querySelectorAll('input, select, textarea').forEach(function (field) {
                        field.disabled = false;
                    });
                }

                if (noChecklistMessage) {
                    noChecklistMessage.textContent = sameStatus
                        ? 'No existing checklist submission is available to edit for the current status.'
                        : 'No checklist is configured for the selected status and asset category. Configure a checklist before saving this hardware.';
                    noChecklistMessage.hidden = !checklistExpanded || activeChecklist !== null;
                }
            }

            function revealStatusChecklist() {
                checklistExpanded = true;
                statusChecklist.hidden = false;
                updateStatusChecklist();
                if (activeChecklist) {
                    activeChecklist.querySelector('input:not([type="hidden"]), select, textarea')?.focus();
                }
            }

            document.getElementById('status_checklist_button')?.addEventListener('click', revealStatusChecklist);
            statusSelect?.addEventListener('change', updateStatusChecklist);
            modelSelect?.addEventListener('change', updateStatusChecklist);

            if (window.jQuery && modelSelect) {
                window.jQuery(modelSelect).on('select2:select', function (event) {
                    var categoryId = event.params?.data?.category?.id;
                    if (categoryId) {
                        modelSelect.dataset.selectedCategoryId = String(categoryId);
                        modelSelect.options[modelSelect.selectedIndex].dataset.categoryId = String(categoryId);
                    }
                    updateStatusChecklist();
                });
                window.jQuery(modelSelect).on('select2:clear', function () {
                    modelSelect.dataset.selectedCategoryId = '';
                    updateStatusChecklist();
                });
            }

            statusChecklist.closest('form')?.addEventListener('submit', function (event) {
                updateStatusChecklist();
                var statusChanged = (statusSelect?.value || '') !== (statusChecklist.dataset.fromStatusId || '');
                if (statusChanged && (!checklistExpanded || !activeChecklist)) {
                    event.preventDefault();
                    revealStatusChecklist();
                    noChecklistMessage.hidden = activeChecklist !== null;
                    if (!activeChecklist) {
                        noChecklistMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });

            updateStatusChecklist();
            if (checklistExpanded) {
                revealStatusChecklist();
            }
        }

        var maintenanceType = document.getElementById('asset_maintenance_type');
        if (maintenanceType && !statusChecklist) {
            var updateVisibleChecklist = function () {
                var selectedStatus = maintenanceType.value.toLowerCase();
                document.querySelectorAll('.event-checklist').forEach(function (panel) {
                    var isVisible = panel.dataset.targetStatus.toLowerCase() === selectedStatus;
                    panel.hidden = !isVisible;
                    panel.querySelectorAll('input, select, textarea').forEach(function (field) {
                        field.disabled = !isVisible;
                    });
                });
            };
            maintenanceType.addEventListener('change', updateVisibleChecklist);
            updateVisibleChecklist();
        }
    </script>
@endif
