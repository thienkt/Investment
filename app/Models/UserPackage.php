<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function owner()
    {
        return $this->hasOne(User::class, 'user_id');
    }

    public function package()
    {
        return $this->hasOne(Package::class, 'package_id');
    }

    public function transaction()
    {
        return $this->belongsToMany(Transaction::class);
    }
}
