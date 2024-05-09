<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'model', 'unit_measure', 'brand', 'quantity', 'description', 
        'price', 'profile_image', 'provider', 'serie', 'observations', 'location', 'category'
    ];
}


