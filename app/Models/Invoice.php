<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;

    use SoftDeletes;


    // Table name (optional, if different from plural model name)
    protected $table = 'invoices';

    // Mass assignable fields
    protected $fillable = [
        'date',
        'invoice',
        'customer',
        'assign',
        'amount',
        'status',
    ];

    // Enable date casting for timestamps and soft deletes
    protected $dates = ['deleted_at'];
}
