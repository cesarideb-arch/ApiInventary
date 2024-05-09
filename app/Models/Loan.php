<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Define the attributes that are mass assignable
    protected $fillable = [
        'product_id',
        'responsible',
        'quantity',
        'date'
    ];

    // Relationship with the Product model
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}





