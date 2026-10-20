<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventChecklistSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_checklist_id',
        'asset_id',
        'from_status_id',
        'to_status_id',
        'rows',
        'evidence_file',
        'submitted_by',
    ];

    protected $casts = [
        'rows' => 'array',
        'evidence_file' => 'array',
    ];

    public function checklist()
    {
        return $this->belongsTo(EventChecklist::class, 'event_checklist_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromStatus()
    {
        return $this->belongsTo(Statuslabel::class, 'from_status_id')->withTrashed();
    }

    public function toStatus()
    {
        return $this->belongsTo(Statuslabel::class, 'to_status_id')->withTrashed();
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function getEvidenceFilesAttribute(): array
    {
        $evidenceFiles = $this->evidence_file;
        if (! is_array($evidenceFiles) || $evidenceFiles === []) {
            return [];
        }

        return isset($evidenceFiles['path'])
            ? [$evidenceFiles]
            : array_values(array_filter($evidenceFiles, 'is_array'));
    }
}
