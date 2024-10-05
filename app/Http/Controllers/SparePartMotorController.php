<?php

namespace App\Http\Controllers;

use App\Exports\SparePartsExport;
use App\Imports\SparePartsImport;
use App\Models\SparePartMotor;
use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SparePartMotorController extends Controller
{

    public function index(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $perPage = $request->input('per_page', 15); // Default 15 items per page

        // Query dengan pencarian dan paginasi
        $query = SparePartMotor::with('motor:id,motor_name')
            ->where(function ($query) use ($searchTerm) {
                if ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%');
                }
            });

        $dataSpareParts = $query->paginate($perPage);

        // Format data untuk output
        $formattedData = $dataSpareParts->map(function ($sparePart) {
            return [
                'id' => $sparePart->id,
                'name' => $sparePart->name,
                'description' => $sparePart->description,
                'price' => $sparePart->price,
                'motor_name' => $sparePart->motor ? $sparePart->motor->motor_name : null,
                'created_at' => $sparePart->created_at,
                'updated_at' => $sparePart->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'results' => $formattedData,
            'pagination' => [
                'total' => $dataSpareParts->total(),
                'per_page' => $dataSpareParts->perPage(),
                'current_page' => $dataSpareParts->currentPage(),
                'last_page' => $dataSpareParts->lastPage(),
            ],
        ], 200);
    }



    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',

                'price' => 'required|numeric|between:0,9999999999.99',
                'motor_name' => 'required|string|exists:motors,motor_name',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validate->errors(),
                ], 400);
            }

            $motor = Motor::where('motor_name', $request->motor_name)->first();

            if (!$motor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Motor not found',
                ], 404);
            }

            $dataSparePart = SparePartMotor::create([
                'name' => $request->name,
                'description' => $request->description,

                'price' => $request->price,
                'motor_id' => $motor->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully created',
                'data' => $dataSparePart,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(string $id)
{
    $dataSparePart = SparePartMotor::with('motor:id,motor_name')->find($id);

    if (!$dataSparePart) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data not found',
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'spare_part' => [
            'id' => $dataSparePart->id,
            'name' => $dataSparePart->name,
            'description' => $dataSparePart->description,

            'price' => $dataSparePart->price,
            'motor_name' => $dataSparePart->motor ? $dataSparePart->motor->motor_name : null,
            'motor_id' => $dataSparePart->motor_id,
            'created_at' => $dataSparePart->created_at,
            'updated_at' => $dataSparePart->updated_at,
        ],
    ], 200);
}


    public function update(Request $request, string $id)
    {
        try {
            $dataSparePart = SparePartMotor::find($id);

            if (!$dataSparePart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',

                'price' => 'required|numeric|between:0,9999999999.99',
                'motor_name' => 'required|string|exists:motors,motor_name',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validate->errors(),
                ], 400);
            }

            $motor = Motor::where('motor_name', $request->motor_name)->first();

            if (!$motor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Motor not found',
                ], 404);
            }

            $dataSparePart->name = $request->name;
            $dataSparePart->description = $request->description;

            $dataSparePart->price = $request->price;
            $dataSparePart->motor_id = $motor->id;

            $dataSparePart->save();

            return response()->json([
                'status' => 'success',
                'spare_part' => $dataSparePart
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sparePart = SparePartMotor::find($id);

        if (!$sparePart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Spare part not found',
            ], 404);
        }

        $sparePart->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Spare part deleted successfully',
        ], 200);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        try {
            Excel::import(new SparePartsImport, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'File imported successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong during the import process.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $searchTerm = $request->input('search', '');

            return Excel::download(new SparePartsExport($searchTerm), 'spare_parts.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong during the export process.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




}
