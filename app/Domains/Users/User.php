<?php

namespace App\Domains\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Domains\Users\Enums\UserTypesEnum;
use App\Domains\Transactions\Transaction;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getHasGeneralAccessAttribute(): bool
    {
        return $this->is_active && ($this->type === UserTypesEnum::ADMIN || $this->type === UserTypesEnum::ORG_MEMBER);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'author_id');
    }
}
