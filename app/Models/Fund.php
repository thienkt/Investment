<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fund extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'historical_data_url',
    ];

    public function credential()
    {
        return $this->belongsTo(Credential::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }
}
