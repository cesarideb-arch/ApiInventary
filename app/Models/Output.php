<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    use HasFactory;
     // Define the attributes that are mass assignable
     protected $fillable = [
        'product_id',
        'responsible',
        'cost',
        'quantity',
        'date',
        'project_id'
    ];

    // Relationship with the Product model
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Optional relationship with the Project model
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
