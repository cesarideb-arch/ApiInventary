<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'article',
        'price',
        'company',
        'phone',
        'email',
        'address',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}