<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfficeController extends Controller
{

    public function index(){

        $dataOffice = Office::all();

        return response()->json([
            'status'=>'success',
            'results'=>$dataOffice
        ],200);
    }

    public function store(Request $request){

        try{
            $validate = Validator::make($request->all(),[
                'branch' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone_office' => 'required|string|digits_between:10,15',
                'latitude' => 'sometimes|required|numeric|between:-90,90 ',
                'longitude' => 'sometimes|required|numeric|between:-180,180 '
            ]);

            if($validate->fails()){
                $response = [
                    'status' => 'error',
                    'data' => $validate->errors(),
                ];
                return response()->json($response,400);
            }

            $dataOffice = Office::create([
                'branch'=> $request->branch,
                'address'=> $request->address,
                'phone_office'=> $request->phone_office,
                'latitude'=> $request->latitude,
                'longitude'=> $request->longitude
            ]);

            return response()->json([
                'messages' => 'office successfully created',
                'data' => $dataOffice,
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'messages' => 'something went wrong',
                'error'=>$e->getMessage()
            ],500);
        }

    }

    public function show(string $id){

        $dataOffice = Office::find($id);

        if(!$dataOffice){
            return response()->json([
                'message' => 'office not found'
            ],404);
        }
            return response()->json([
                'office'=>$dataOffice
            ],200);

    }

    public function update(Request $request, string $id){
        try{
            $dataOffice = Office::find($id);

            if(!$dataOffice){
                return response()->json([
                    'message' => 'office not found'
                ],404);
            }

            $validate = Validator::make($request->all(),[
                'branch' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone_office' => 'required|string|digits_between:10,15',
                'latitude' => 'sometimes|required|numeric|between:-90,90 ',
                'longitude' => 'sometimes|required|numeric|between:-180,180 '

            ]);

            if($validate->fails()){
                $response = [
                    'status' => 'error',
                    'data' => $validate->errors(),
                ];
                return response()->json($response,400);
            }

            $dataOffice->branch = $request->branch;
            $dataOffice->address = $request->address;
            $dataOffice->phone_office = $request->phone_office;
            $dataOffice->latitude = $request->latitude;
            $dataOffice->longitude = $request->longitude;

            $dataOffice -> save();

            return response()->json([
                'office'=>$dataOffice
            ],200);

        } catch(\Exception $e){
            return response()->json([
                'messages'=>'something went wrong',
                'error' => $e->getMessage()
              ],500);
        }

    }

    public function destroy(string $id) {

        $dataOffice = Office::find($id);

        if(!$dataOffice){
            return response()->json([
                'messages'=>'Office not found'
            ],404);
        }

        $dataOffice -> delete();

        return response()->json([
            'messages'=> 'Office sucessfully deleted'
        ],200);
    }

}
