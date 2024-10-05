<?php

namespace App\Imports;

use App\Models\SparePartMotor;
use App\Models\Motor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SparePartsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            $motor = Motor::where('motor_name', $row['motor_name'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            // Optionally log the error or handle it as needed
            return null; // Skip this row
        }

        return new SparePartMotor([
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'motor_id' => $motor->id,
        ]);
    }
}


