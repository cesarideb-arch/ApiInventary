<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'materials'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'category_material');
    }
}