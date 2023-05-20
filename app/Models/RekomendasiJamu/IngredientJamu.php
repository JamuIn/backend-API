<?php

namespace App\Models\RekomendasiJamu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientJamu extends Model
{
    use HasFactory;
    protected $table = 'ingredient_jamu';
    protected $guarded = [];
    
}
