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


// class Product extends Model
// {
//     protected $fillable = [
//         'name',
//         'model',
//         'measurement_unit',
//         'brand',
//         'price',
//         'quantity',
//         'description',
//         'product_image',
//         'serie',
//         'observations',
//         'location',
//         'category_id',
//         'supplier_id',
//     ];

//     public function category()
//     {
//         return $this->belongsTo(Category::class);
//     }

//     public function supplier()
//     {
//         return $this->belongsTo(Supplier::class);
//     }

//     public function entrances()
//     {
//         return $this->belongsToMany(Entrance::class, 'products_entrances', 'product_id', 'entrance_id');
//     }

//     public function outputs()
//     {
//         return $this->belongsToMany(Output::class, 'products_outputs', 'product_id', 'output_id');
//     }

//     public function loans()
//     {
//         return $this->hasMany(Loan::class);
//     }
// }