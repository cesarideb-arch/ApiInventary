<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrance extends Model
{
    use HasFactory;

// Definir los atributos que son asignables masivamente
protected $fillable = [
    'product_id',
    'responsible',
    'cost',
    'quantity',
    'date',
    'project_id'
];

// Relación con el modelo Product
public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

// Relación opcional con el modelo Project
public function project()
{
    return $this->belongsTo(Project::class, 'project_id');
}

}
