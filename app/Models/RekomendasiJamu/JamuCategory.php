<?php

namespace App\Models\RekomendasiJamu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamuCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function jamu()
    {
        return $this->hasMany(Jamu::class);
    }
}
