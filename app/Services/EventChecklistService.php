<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Actionlog;
use App\Models\EventChecklist;
use App\Models\EventChecklistSubmission;
use Illuminate\Support\MessageBag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventChecklistService
{
    public function forTransition(Asset $asset, ?int $fromStatusId, int $toStatusId): ?EventChecklist
    {
        $categoryId = $asset->model?->category_id;

        if ($fromStatusId === $toStatusId) {
            return null;
        }

        return EventChecklist::query()
            ->where('to_status_id', $toStatusId)
            ->where(function ($query) use ($fromStatusId) {
                $query->where('from_status_id', $fromStatusId)
                    ->orWhereNull('from_status_id');
            })
            ->where(function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId)
                    ->orWhereNull('category_id');
            })
            ->orderByRaw('category_id IS NULL')
            ->orderByRaw('from_status_id IS NULL')
            ->first();
    }

    public function forStatus(Asset $asset, int $statusId, ?int $fromStatusId = null): ?EventChecklist
    {
        $categoryId = $asset->model?->category_id;

        return EventChecklist::query()
            ->where('to_status_id', $statusId)
            ->where(function ($query) use ($fromStatusId) {
                if ($fromStatusId === null) {
                    $query->whereNull('from_status_id');
                } else {
                    $query->where('from_status_id', $fromStatusId)
                        ->orWhereNull('from_status_id');
                }
            })
            ->where(function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId)
                    ->orWhereNull('category_id');
            })
            ->orderByRaw('category_id IS NULL')
            ->orderByRaw('from_status_id IS NULL')
            ->orderBy('id')
            ->first();
    }

    public function forStatusName(Asset $asset, string $statusName): ?EventChecklist
    {
        $status = \App\Models\Statuslabel::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($statusName)])
            ->first();

        return $status ? $this->forStatus($asset, (int) $status->id) : null;
    }

    public function validate(EventChecklist $checklist, mixed $rows, mixed $files = null, bool $requireComplete = true): MessageBag
    {
        $errors = new MessageBag();
        if (is_array($rows) && array_key_exists((string) $checklist->id, $rows)) {
            $rows = $rows[$checklist->id];
        }
        if (is_array($files) && array_key_exists((string) $checklist->id, $files)) {
            $files = $files[$checklist->id];
        }
        $rows = is_array($rows) ? array_values($rows) : [];
        $checklistFiles = $this->checklistFiles($checklist, $files);
        $templateRows = $checklist->rows ?: [[]];

        if ($requireComplete && count($rows) !== count($templateRows)) {
            $errors->add('event_checklist', 'Complete every checklist row before saving.');
        }

        $columns = collect($checklist->columns ?? []);

        foreach ($columns as $column) {
            $key = $column['key'] ?? null;
            if (!$key) {
                continue;
            }

            $label = $column['label'] ?? $key;
            foreach ($templateRows as $rowIndex => $templateRow) {
                $value = is_array($rows[$rowIndex] ?? null) ? ($rows[$rowIndex][$key] ?? null) : null;
                if (is_array($value)) {
                    $value = implode('', $value);
                }

                $required = $requireComplete && (!empty($column['required']) || $key === 'result');
                if ($required && ($value === null || trim((string) $value) === '')) {
                    $errors->add("event_checklist.rows.$rowIndex.$key", "$label is required.");
                }

                if ($key === 'result'
                    && $value !== null
                    && $value !== ''
                    && !in_array($value, $column['options'] ?? [], true)) {
                    $errors->add("event_checklist.rows.$rowIndex.$key", 'Select a valid checklist result.');
                }
            }
        }

        $maxFileSize = \App\Helpers\Helper::file_upload_max_size();
        foreach ($checklistFiles as $fileIndex => $file) {
            if (! $file->isValid()) {
                $errors->add("event_checklist.file.$fileIndex", 'The evidence file could not be uploaded.');
            } else {
                $fileValidator = Validator::make(
                    ['file' => $file],
                    ['file' => 'file|mimes:png,gif,jpg,svg,jpeg,doc,docx,pdf,txt,zip,rar,xls,xlsx,lic,xml,rtf,json,webp,avif|max:'.$maxFileSize]
                );
                if ($fileValidator->fails()) {
                    $errors->add("event_checklist.file.$fileIndex", $fileValidator->errors()->first('file'));
                }
            }
        }

        return $errors;
    }

    public function hasResponses(EventChecklist $checklist, mixed $rows, mixed $files = null): bool
    {
        if ($this->checklistFiles($checklist, $files) !== []) {
            return true;
        }

        if (is_array($rows) && array_key_exists((string) $checklist->id, $rows)) {
            $rows = $rows[$checklist->id];
        }
        if (! is_array($rows)) {
            return false;
        }

        foreach (array_values($rows) as $rowIndex => $row) {
            if (! is_array($row)) {
                continue;
            }

            $templateRow = $checklist->rows[$rowIndex] ?? [];
            foreach ($checklist->columns ?? [] as $column) {
                $key = $column['key'] ?? null;
                if (! $key || ! array_key_exists($key, $row)) {
                    continue;
                }

                $value = $row[$key];
                if (($column['type'] ?? null) === 'checkbox') {
                    if (in_array($value, [true, 1, '1', 'true', 'on'], true)) {
                        return true;
                    }
                    continue;
                }

                if ($key !== 'result'
                    && isset($templateRow[$key])
                    && (string) $value === (string) $templateRow[$key]) {
                    continue;
                }

                if ($value !== null && trim((string) $value) !== '') {
                    return true;
                }
            }
        }

        return false;
    }

    public function record(
        EventChecklist $checklist,
        Asset $asset,
        ?int $fromStatusId,
        int $toStatusId,
        mixed $rows,
        ?int $submittedBy,
        mixed $files = null
    ): EventChecklistSubmission {
        if (is_array($rows) && array_key_exists((string) $checklist->id, $rows)) {
            $rows = $rows[$checklist->id];
        }
        $rows = is_array($rows) ? array_values($rows) : [];
        foreach ($rows as &$row) {
            if (! is_array($row)) {
                continue;
            }

            foreach ($checklist->columns ?? [] as $column) {
                if (($column['type'] ?? null) === 'checkbox') {
                    $row[$column['key']] = in_array($row[$column['key']] ?? false, [true, 1, '1', 'true', 'on'], true);
                }
            }
        }
        unset($row);
        $evidenceFiles = $this->storeEvidenceFiles($checklist, $asset, $this->checklistFiles($checklist, $files));

        return $checklist->submissions()->create([
            'asset_id' => $asset->id,
            'from_status_id' => $fromStatusId,
            'to_status_id' => $toStatusId,
            'rows' => $rows,
            'evidence_file' => $evidenceFiles ?: null,
            'submitted_by' => $submittedBy,
        ]);
    }

    public function updateSubmission(
        EventChecklistSubmission $submission,
        mixed $rows,
        mixed $files = null
    ): EventChecklistSubmission {
        $checklist = $submission->checklist;
        if (! $checklist) {
            throw new \RuntimeException('The event checklist for this submission no longer exists.');
        }

        if (is_array($rows) && array_key_exists((string) $checklist->id, $rows)) {
            $rows = $rows[$checklist->id];
        }
        $rows = is_array($rows) ? array_values($rows) : [];
        foreach ($submission->rows ?? [] as $rowIndex => $existingRow) {
            if (isset($rows[$rowIndex]) && ! empty($existingRow['evidence_file']['path'])) {
                $rows[$rowIndex]['evidence_file'] = $existingRow['evidence_file'];
            }
        }
        $newFiles = $this->checklistFiles($checklist, $files);
        $newEvidenceFiles = $newFiles
            ? $this->storeEvidenceFiles($checklist, $submission->asset, $newFiles)
            : [];
        $existingEvidenceFiles = $submission->evidence_files;

        $submission->rows = $rows;
        $submission->evidence_file = array_merge($existingEvidenceFiles, $newEvidenceFiles) ?: null;
        if (! $submission->save()) {
            throw new \RuntimeException('Unable to update the event checklist submission.');
        }

        return $submission;
    }

    private function checklistFiles(EventChecklist $checklist, mixed $files): array
    {
        if (is_array($files) && array_key_exists((string) $checklist->id, $files)) {
            $files = $files[$checklist->id];
        }

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        return array_values(array_filter(
            $files,
            fn ($file) => $file instanceof UploadedFile
        ));
    }

    private function storeEvidenceFiles(EventChecklist $checklist, Asset $asset, array $files): array
    {
        if ($files === []) {
            return [];
        }

        $directory = 'private_uploads/assets';
        if (! Storage::exists($directory)) {
            Storage::makeDirectory($directory, 775);
        }

        $evidenceFiles = [];
        foreach ($files as $file) {
            if (! $file->isValid()) {
                throw new \RuntimeException('The event checklist evidence file could not be uploaded.');
            }

            $extension = $file->guessExtension() ?? strtolower($file->getClientOriginalExtension());
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = $asset->uploadFilenamePrefix().'-'.Str::random(8).'-'.Str::slug($originalName).'.'.$extension;

            if ($file->getMimeType() === 'image/svg+xml') {
                $sanitizer = new \enshrined\svgSanitize\Sanitizer();
                $contents = $sanitizer->sanitize((string) file_get_contents($file->getRealPath()));
                $stored = Storage::put($directory.'/'.$filename, $contents);
            } else {
                $stored = Storage::putFileAs($directory, $file, $filename);
            }

            if (! $stored) {
                throw new \RuntimeException('Unable to save the event checklist evidence file.');
            }

            if (! $asset->logUpload($filename, 'Event checklist evidence: '.$checklist->name)) {
                Storage::delete($directory.'/'.$filename);
                throw new \RuntimeException('Unable to attach the event checklist evidence file to the asset.');
            }

            $upload = $asset->uploads()->where('filename', $filename)->first();
            if (! $upload) {
                Storage::delete($directory.'/'.$filename);
                throw new \RuntimeException('Unable to find the asset evidence file record.');
            }

            $evidenceFiles[] = [
                'path' => $directory.'/'.$filename,
                'filename' => $filename,
                'name' => $file->getClientOriginalName(),
                'upload_id' => $upload->id,
            ];
        }

        return $evidenceFiles;
    }

}
