<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Country extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
