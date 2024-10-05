<?php

namespace App\Exports;

use App\Models\SparePartMotor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SparePartsExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function __construct($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    public function query()
    {
        return SparePartMotor::query()
            ->with('motor:id,motor_name')
            ->where(function ($query) {
                if ($this->searchTerm) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
                }
            });
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
}
