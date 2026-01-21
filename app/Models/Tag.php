<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pet> $pets
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'pet_tag');
    }
}
