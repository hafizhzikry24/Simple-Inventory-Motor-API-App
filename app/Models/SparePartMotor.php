<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePartMotor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'price',
        'motor_id'
    ];

    public function motor() {
        return $this->belongsTo(Motor::class, 'motor_id');
    }

}
