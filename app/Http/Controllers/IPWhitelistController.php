<?php

namespace App\Http\Controllers;

use App\Models\IpWhiteList;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IPWhitelistController extends Controller
{
    // public function index() {
    //     try {
    //         $Ipwhitelists = IpWhiteList::with(['office' => function($query) {
    //             $query->select('id', 'branch'); // Explicitly selecting 'id' and 'branch' from the 'office' table
    //         }])->get();

    //         return response()->json([
    //             'status' => 'success',
    //             'results' => $Ipwhitelists
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function index()
    {
        // Mengambil semua data IpWhiteList beserta kantor yang terkait
        $ipWhiteLists = IpWhiteList::with('office')->get();

        // Membuat array untuk menyimpan hasil
        $resultArray = [];

        foreach ($ipWhiteLists as $ipWhiteList) {
            $resultArray[] = [
                'id' => $ipWhiteList->id,
                'ip_address' => $ipWhiteList->ip_address,
                'description' => $ipWhiteList->description,
                'office_id' => $ipWhiteList->office->id,
                'branch' => $ipWhiteList->office->branch,
                'created_at' => $ipWhiteList->created_at,
                'updated_at' => $ipWhiteList->updated_at,
            ];
        }

        // Mengembalikan data sebagai JSON
        return response()->json([
            'status' => 'success',
            'results' => $resultArray,
        ], 200);
    }




    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'ip_address'  => 'required|ipv4',  // Validasi format IP
                'description' => 'nullable|string',
                'office_id'   => 'required|exists:offices,id',  // Pastikan office_id ada di tabel offices
            ]);

            // Membuat dan menyimpan IP whitelist baru
            $ipWhiteList = IpWhiteList::create([
                'ip_address'  => $validatedData['ip_address'],
                'description' => $validatedData['description'],
                'office_id'   => $validatedData['office_id'],  // Relasi dengan Office
            ]);

            // Mengembalikan respons sukses
            return response()->json([
                'message' => 'IP White List created successfully',
                'data'    => $ipWhiteList
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(string $id) {
        try {
            // Ambil data IP whitelist dengan relasi office
            $ipWhitelist = IpWhiteList::with('office:id,branch')->find($id);

            // Periksa apakah data ditemukan
            if (!$ipWhitelist) {
                return response()->json([
                    'message' => 'IP not found'
                ], 404);
            }


            return response()->json([
                'ipwhitelist' => [
                    'ip_address' => $ipWhitelist->ip_address,
                    'description' => $ipWhitelist->description,
                    'branch' => $ipWhitelist->office ? $ipWhitelist->office->branch : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, string $id) {
        try {
            $ipWhitelist = IpWhiteList::find($id);
            if (!$ipWhitelist) {
                return response()->json([
                    'message' => 'IP address not found'
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'ip_address' => 'required|ipv4|unique:ip_whitelists,ip_address,' . $id,
                'description' => 'required|string|max:255',
                'office_branch' => 'required|string|exists:offices,branch'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validate->errors(),
                ], 400);
            }

            $office = Office::where('branch', $request->office_branch)->first();
            if (!$office) {
                return response()->json([
                    'message' => 'Office branch not found',
                ], 404);
            }

            $ipWhitelist->ip_address = $request->ip_address;
            $ipWhitelist->description = $request->description;
            $ipWhitelist->office_id = $office->id;
            $ipWhitelist->save();

            return response()->json([
                'message' => 'IP address successfully updated',
                'data' => $ipWhitelist,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id) {
        try {
            $Ipwhitelists = IpWhiteList::find($id);
            if (!$Ipwhitelists) {
                return response()->json([
                    'message' => 'IP not found'
                ], 404);
            }

            $Ipwhitelists->delete();

            return response()->json([
                'message' => 'IP address successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
