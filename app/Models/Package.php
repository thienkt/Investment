<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function owners()
    {
        return $this->belongsToMany(User::class, 'user_package')->withPivot('investment_amount');
    }

    public function funds()
    {
        return $this->belongsToMany(Fund::class, 'package_fund')->withPivot('allocation_percentage');
    }

    /**
     * Scope a query to only include default packages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', '=', 1);
    }
}
