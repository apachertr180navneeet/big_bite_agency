<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;

    
    use SoftDeletes;


    // Table name (optional, if different from plural model name)
    protected $table = 'customers';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'firm',
        'email',
        'phone',
        'gst',
        'address1',
        'address2',
        'city',
        'state',
        'discount',
        'status',
    ];

    // Enable date casting for timestamps and soft deletes
    protected $dates = ['deleted_at'];
}
