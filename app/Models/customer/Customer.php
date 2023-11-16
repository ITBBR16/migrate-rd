<?php

namespace App\Models\customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $connection = 'rumahdrone_customer';
    protected $table = 'customer';
    protected $guarded = ['id'];

    
}
