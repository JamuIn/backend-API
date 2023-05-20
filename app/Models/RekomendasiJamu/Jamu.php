<?php

namespace App\Models\RekomendasiJamu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jamu extends Model
{
    use HasFactory;
    protected $table = 'jamu';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\Models\RekomendasiJamu\JamuCategory');
    }


    public function ingredients()
    {
        return $this->belongsToMany('App\Models\RekomendasiJamu\Ingredient');
    }
}
