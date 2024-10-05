<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpWhiteList extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ip_whitelists';

    protected $fillable = [
        'ip_address',
        'description',
        'office_id',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

}
