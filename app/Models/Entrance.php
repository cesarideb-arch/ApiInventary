<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


    class Entrance extends Model
    {
        protected $fillable = ['project_id', 'responsible', 'quantity', 'date', 'description', 'product_id', 'folio', 'price', 'user_id'];
    
        public function project()
        {
            return $this->belongsTo(Project::class);
        }
        public function product()
        {
            return $this->belongsTo(Product::class);
        }
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    }
