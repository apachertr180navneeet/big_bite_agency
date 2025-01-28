<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory;

    use SoftDeletes;


    // Table name (optional, if different from plural model name)
    protected $table = 'receipts';

    // Mass assignable fields
    protected $fillable = [
        'date',
        'receipt',
        'bill_id',
        'amount',
        'discount',
        'full_payment',
        'remaing_amount',
        'manager_status',
        'remark',
        'mode',
        'status',
    ];

    // Enable date casting for timestamps and soft deletes
    protected $dates = ['deleted_at'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'bill_id', 'id');
    }
}
