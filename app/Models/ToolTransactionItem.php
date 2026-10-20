<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

/**
 * Model for Tool Transaction Items.
 *
 * @version    v1.0
 */
class ToolTransactionItem extends Model implements ICompanyableChild
{
    use HasFactory;
    use SoftDeletes;
    use CompanyableChildTrait;
    use ValidatingTrait;

    protected $table = 'tool_transaction_items';
    
    protected $rules = [
        'transaction_id' => 'required|integer',
        'asset_id' => 'required|integer',
        'notes' => 'string|nullable',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'asset_id',
        'event_checklist_submission_id',
        'notes',
    ];

    public function getCompanyableParents()
    {
        return ['transaction', 'asset'];
    }

    /**
     * Transaction this item belongs to
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Models\ToolTransaction::class, 'transaction_id');
    }

    /**
     * Asset involved in this transaction
     */
    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class, 'asset_id');
    }

    public function checklistSubmission()
    {
        return $this->belongsTo(EventChecklistSubmission::class, 'event_checklist_submission_id');
    }
}
