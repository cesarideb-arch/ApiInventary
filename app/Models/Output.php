<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    protected $fillable = ['project_id', 'responsible', 'quantity', 'date', 'product_id', 'description', 'user_id'];

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
