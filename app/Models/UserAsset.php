<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'amount',
    ];

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function package()
    {
        return $this->belongsTo(UserPackage::class);
    }
}
