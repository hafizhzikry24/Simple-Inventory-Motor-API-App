<?php

namespace App\Exports;

use App\Models\SparePartMotor;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SparePartsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    public function query()
    {
        Log::info('Executing query with search term: ' . $this->searchTerm);

        $query = SparePartMotor::query()
            ->select('spare_part_motors.*', 'motors.motor_name')
            ->leftJoin('motors', 'motors.id', '=', 'spare_part_motors.motor_id')
            ->where(function ($query) {
                if ($this->searchTerm) {
                    Log::info('Search term applied: ' . $this->searchTerm);

                    $query->where('spare_part_motors.name', 'like', '%' . $this->searchTerm . '%')
                          ->orWhere('spare_part_motors.description', 'like', '%' . $this->searchTerm . '%');
                }
            });

        Log::info('Query executed: ' . $query->toSql());

        return $query;
    }




    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Price',
            'Motor Name',
            'Created At',
            'Updated At',
        ];
    }

    public function map($sparePartMotor): array
    {
        Log::info('Mapping spare part data for: ' . $sparePartMotor->name);

        return [
            $sparePartMotor->id,
            $sparePartMotor->name,
            $sparePartMotor->description,
            $sparePartMotor->price,
            $sparePartMotor->motor ? $sparePartMotor->motor->motor_name : 'N/A',
            $sparePartMotor->created_at,
            $sparePartMotor->updated_at,
        ];
    }

}
