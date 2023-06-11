<?php

namespace App\Models\RekomendasiJamu;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jamu extends Model
{
    use HasFactory;
    protected $table = 'jamu';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(JamuCategory::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
