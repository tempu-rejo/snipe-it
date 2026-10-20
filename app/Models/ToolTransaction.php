<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

/**
 * Model for Tool Transactions.
 *
 * @version    v1.0
 */
class ToolTransaction extends Model implements ICompanyableChild
{
    use HasFactory;
    use SoftDeletes;
    use CompanyableChildTrait;
    use ValidatingTrait;

    protected $table = 'tool_transactions';
    
    protected $rules = [
        'transaction_type' => 'required|in:checkin,checkout,maintenance',
        'document_number' => 'required|string|max:20',
        'user_id' => 'required|integer',
        'notes' => 'string|nullable',
        'transaction_date' => 'required|date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_type',
        'document_number', 
        'user_id',
        'location_id',
        'location_detail',
        'on_behalf',
        'notes',
        'transaction_date',
        'created_by',
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = [
        'transaction_type',
        'document_number',
        'notes',
        'transaction_date',
    ];

    /**
     * The relations and their attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableRelations = [
        'user' => ['first_name', 'last_name', 'username'],
        'createdBy' => ['first_name', 'last_name', 'username'],
    ];

    public function getCompanyableParents()
    {
        return ['user'];
    }

    /**
     * User that this transaction is for
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * User who created this transaction
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Location associated with this transaction
     */
    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }

    /**
     * Transaction items
     */
    public function items()
    {
        return $this->hasMany(\App\Models\ToolTransactionItem::class, 'transaction_id');
    }

    /**
     * Generate document number based on transaction type
     */
    public static function generateDocumentNumber($type)
    {
        $prefix = '';
        switch ($type) {
            case 'checkin':
                $prefix = 'TRIN';
                break;
            case 'checkout':
                $prefix = 'TROUT';
                break;
            case 'maintenance':
                $prefix = 'TRSRV';
                break;
        }

        $yearMonth = date('ym');
        
        // Get the last increment for this type and year/month
        $lastTransaction = self::where('transaction_type', $type)
            ->where('document_number', 'like', $prefix . '-' . $yearMonth . '-%')
            ->orderBy('document_number', 'desc')
            ->first();

        $increment = 1;
        if ($lastTransaction) {
            $lastNumber = $lastTransaction->document_number;
            $lastIncrement = (int) substr($lastNumber, -5);
            $increment = $lastIncrement + 1;
        }

        return $prefix . '-' . $yearMonth . '-' . str_pad($increment, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for checkin transactions
     */
    public function scopeCheckin($query)
    {
        return $query->where('transaction_type', 'checkin');
    }

    /**
     * Scope for checkout transactions
     */
    public function scopeCheckout($query)
    {
        return $query->where('transaction_type', 'checkout');
    }

    /**
     * Scope for maintenance transactions
     */
    public function scopeMaintenance($query)
    {
        return $query->where('transaction_type', 'maintenance');
    }
}
