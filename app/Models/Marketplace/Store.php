<?php

namespace App\Models\Marketplace;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image',
        'payment_address',
    ];

    /**
     * Get the user associated with the store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
