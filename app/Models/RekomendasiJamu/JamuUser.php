<?php

namespace App\Models\RekomendasiJamu;

use App\Models\User;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamuUser extends Model
{
    use HasFactory, HasRoles;
    protected $table = 'jamu_user';
    protected $guarded = [];
}
