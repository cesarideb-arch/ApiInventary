<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    protected $fillable = ['project_id', 'responsible', 'quantity', 'date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
