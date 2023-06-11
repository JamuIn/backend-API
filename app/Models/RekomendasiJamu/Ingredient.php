<?php

namespace App\Models\RekomendasiJamu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Ingredient extends Model
{
    use HasFactory, HasRoles;
    protected $guarded = [];
}
