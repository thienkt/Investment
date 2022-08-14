<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'investment_amount'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function funds()
    {
       return $this->belongsToMany(Fund::class)->withPivot('allocation_percentage');
    }
}
