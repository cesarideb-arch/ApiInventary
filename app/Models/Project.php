<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'company_name',
        'rfc',
        'address',
        'ubicacion',
        'phone_number',
        'email',
        'client_name',
    ];

    public function entrances()
    {
        return $this->hasMany(Entrance::class);
    }

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }
}