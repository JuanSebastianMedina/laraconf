<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'city',
        'country',
        'postal_code',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'id' => 'integer',
    ];

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }
}
