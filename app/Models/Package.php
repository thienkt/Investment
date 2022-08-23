<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function owner()
    {
        return $this->belongsToMany(User::class)->withPivot('investment_amount');
    }

    public function funds()
    {
        return $this->belongsToMany(Fund::class)->withPivot('allocation_percentage');
    }
}
