<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


    class Entrance extends Model
    {
        protected $fillable = ['project_id', 'responsible', 'quantity', 'date', 'description', 'product_id', 'folio', 'price'];
    
        public function project()
        {
            return $this->belongsTo(Project::class);
        }
        public function product()
        {
            return $this->belongsTo(Product::class);
        }
    }
