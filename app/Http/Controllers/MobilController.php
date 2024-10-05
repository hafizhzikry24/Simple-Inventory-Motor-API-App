<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MobilController extends Controller
{
    public function index() {
        $dataMobils = Mobil::with('user:id,name,phone_number')->get();

        return response()->json([
            'status' => 'success',
            'results' => $dataMobils
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            // Validasi request
            $validate = Validator::make($request->all(), [
                'mobil_name' => 'required|string|max:255',
                'mobil_model' => 'required|string|max:255',
                'price' => 'required|numeric|between:0,9999999999.99',
                'image' => 'required|file|mimes:png,jpg,jpeg|max:4096',
                'user_name' => 'required|string|exists:users,name',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validate->errors(),
                ], 400);
            }

            // Menangani file gambar
            $file = $request->file('image');
            $filename = date('Y-m-d') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filepath = $file->storeAs('public/image', $filename);

            // Menghilangkan "public/" dari path yang disimpan
            $storedPath = str_replace('public/', '', $filepath);

            // Menemukan user
            $user = User::where('name', $request->user_name)->first();

            // Membuat entri Mobil
            $dataMobils = Mobil::create([
                'mobil_name' => $request->mobil_name,
                'mobil_model' => $request->mobil_model,
                'price' => $request->price,
                'image' => $storedPath,
                'user_id' => $user->id,
            ]);

            // Membuat URL untuk mengakses gambar
            $imageUrl = asset('storage/image/' . $filename);

            return response()->json([
                'messages' => 'Data successfully created',
                'data' => $dataMobils,
                'image_url' => $imageUrl,
            ], 201); // Status code untuk created

        } catch (\Exception $e) {
            return response()->json([
                'messages' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $dataMobil = Mobil::with('user:id,name,phone_number')->find($id);

        if (!$dataMobil) {
            return response()->json([
                'error' => 'Mobil not found'
            ], 404);
        }

        // Menghasilkan URL untuk gambar
        $imageUrl = $dataMobil->image ? asset('storage/' . $dataMobil->image) : null;

        return response()->json([
            'mobil' => [
                'mobil_name' => $dataMobil->mobil_name,
                'mobil_model' => $dataMobil->mobil_model,
                'price' => $dataMobil->price,
                'image' => $imageUrl,
                'user_name' => $dataMobil->user->name,
                'user_phone_number' => $dataMobil->user->phone_number,
            ]
        ], 200);
    }

    public function update(Request $request, string $id){
        try{
            $dataMobil = Mobil::find($id);

            if(!$dataMobil){
                return response()->json([
                    'message' => 'Data not found'
                ], 404);
            }

            $validate = Validator::make($request->all(),[
                'mobil_name' => 'required|string|max:255',
                'mobil_model' => 'required|string|max:255',
                'price' => 'required|numeric|between:0,9999999999.99',
                'image' => 'nullable|file|mimes:png,jpg,jpeg|max:4096',
                'user_name' => 'required|string|exists:users,name',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validate->errors(),
                ], 400);
            }

            $user = User::where('name', $request->user_name)->first();

            $dataMobil->mobil_name = $request->mobil_name;
            $dataMobil->mobil_model = $request->mobil_model;
            $dataMobil->price = $request->price;
            $dataMobil->user_id = $user->id;

            if($request->hasFile('image')){
                // Menghapus gambar lama
                if($dataMobil->image) {
                    Storage::delete('public/' . $dataMobil->image);
                }

                $file = $request->file('image');
                $filename = date('Y-m-d') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filepath = $file->storeAs('public/image', $filename);
                $storedPath = str_replace('public/', '', $filepath);

                $dataMobil->image = $storedPath;
            }

            $dataMobil->save();

            $imageUrl = $dataMobil->image ? asset('storage/' . $dataMobil->image) : null;

            return response()->json([
                'mobil' => $dataMobil,
                'image_url' => $imageUrl,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'messages' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id){
        $dataMobil = Mobil::find($id);

        if(!$dataMobil){
            return response()->json([
                'message' => 'Data not found'
            ],404);
        }

        // Menghapus file gambar jika ada
        if($dataMobil->image) {
            Storage::delete('public/' . $dataMobil->image);
        }

        $dataMobil->delete();

        return response()->json([
            'messages'=> 'Data successfully deleted'
        ],200);
    }
}

