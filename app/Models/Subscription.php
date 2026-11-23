<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //add fillable fields to the Subscription model
    //so laravel allows saving data to the database
    protected $fillable = [
        'name',
        'price',
        'frequency',
        'category',
        'billing',
        'firstBill',
        'paymentMethod',
        'color',   
    ];
}
