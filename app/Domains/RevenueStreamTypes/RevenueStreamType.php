<?php

namespace App\Domains\RevenueStreamTypes;

use Illuminate\Database\Eloquent\Model;
use App\Domains\RevenueStreams\RevenueStream;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RevenueStreamType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function revenueStreams(): HasMany
    {
        return $this->hasMany(RevenueStream::class);
    }
}
