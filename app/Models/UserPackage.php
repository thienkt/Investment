<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'investment_amount',
        'balance'
    ];

    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'user_id');
    }

    public function package(): HasOne
    {
        return $this->hasOne(Package::class, 'package_id');
    }

    public function transaction(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class);
    }
}
