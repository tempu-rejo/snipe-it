<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'from_status_id',
        'to_status_id',
        'template_id',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function fromStatus()
    {
        return $this->belongsTo(Statuslabel::class, 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(Statuslabel::class, 'to_status_id');
    }

    public function template()
    {
        return $this->belongsTo(EventChecklistTemplate::class, 'template_id');
    }

    public function getColumnsAttribute(): array
    {
        return $this->template?->columns ?? [];
    }

    public function getRowsAttribute(): array
    {
        return $this->template?->rows ?? [];
    }

    public function submissions()
    {
        return $this->hasMany(EventChecklistSubmission::class);
    }
}
