<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    // Use the HasFactory trait;
    use HasFactory;

    // Define the attributes that are mass assignable
    protected $fillable = [
        'article',
        'price',
        'company',
        'phone',
        'email',
        'address',
    ];
}
