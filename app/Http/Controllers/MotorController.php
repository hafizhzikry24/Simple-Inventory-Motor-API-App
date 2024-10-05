<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class MotorController extends Controller
{
    public function index()
    {
        $dataMotors = Motor::with('user:id,name,email')->get();

        return response()->json([
            'status' => 'success',
            'results' => $dataMotors
        ], 200);
    }

    public function store(Request $request) {
        try {

            $validate = Validator::make($request->all(), [
                'motor_name' => 'required|string|max:255',
                'motor_model' => 'required|string|max:255',
                'price' => 'required|numeric|between:0,9999999999.99',
                'user_name' => 'required|string|exists:users,name',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => 'error',
                    'data' => $validate->errors(),
                ];
                return response()->json($response, 400);
            }

            $user = User::where('name', $request->user_name)->first();


            if (!$user) {
                return response()->json([
                    'messages' => 'User not found',
                ], 404);
            }


            $dataMotors = Motor::create([
                'motor_name' => $request->motor_name,
                'motor_model' => $request->motor_model,
                'price' => $request->price,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'messages' => 'Data successfully created',
                'data' => $dataMotors,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'messages' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function show(string $id)
    {
        $dataMotors = Motor::with('user:id,name,address')->find($id);
        if (!$dataMotors) {
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return response()->json([
            'motor' => [
                'motor_name' => $dataMotors->motor_name,
                'motor_model' => $dataMotors->motor_model,
                'price' => $dataMotors->price,
                'user_name' => $dataMotors->user ? $dataMotors->user->name : null,
                'user_address' => $dataMotors->user ? $dataMotors->user->address : null,
            ]
        ], 200);
    }


    public function update(Request $request, string $id) {
        try {
            $dataMotors = Motor::find($id);
            if (!$dataMotors) {
                return response()->json([
                    'message' => 'data not found'
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'motor_name' => 'required|string|max:255',
                'motor_model' => 'required|string|max:255',
                'price' => 'required|numeric|between:0,999999999999.99',
                'user_name' => 'nullable|string|exists:users,name',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validate->errors(),
                ], 400);
            }

            $user = User::where('name', $request->user_name)->first();

            if (!$user) {
                return response()->json([
                    'messages' => 'User not found',
                ], 404);
            }

            $dataMotors->motor_name = $request->motor_name;
            $dataMotors->motor_model = $request->motor_model;
            $dataMotors->price = $request->price;
            $dataMotors->user_id = $user->id;

            $dataMotors->save();

            return response()->json([
                'motor' => $dataMotors
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'messages' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id){
        $dataMotors = Motor::find($id);
        if(!$dataMotors){
            return response()->json([
                'message' => 'data not found'
            ],404);

        }

        $dataMotors->delete();

        return response()->json([
            'messages'=> 'data sucessfully deleted'
        ],200);
    }
}
