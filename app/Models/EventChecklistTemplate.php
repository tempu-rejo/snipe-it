<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventChecklistTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'columns',
        'rows',
    ];

    protected $casts = [
        'columns' => 'array',
        'rows' => 'array',
    ];

    public function eventChecklists()
    {
        return $this->hasMany(EventChecklist::class, 'template_id')->withTrashed();
    }
}
