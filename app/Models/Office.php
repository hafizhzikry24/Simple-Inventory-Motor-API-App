<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch',
        'address',
        'phone_office',
        'latitude',
        'longitude'
    ];
    public function ipwhitelists() {
        return $this->hasMany(IpWhiteList::class);
    }
}
