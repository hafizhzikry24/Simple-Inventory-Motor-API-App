<?php

namespace App\Http\Controllers;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(){
        $users = User::all();

        return response()->json([
            'status'=>'success',
            'results'=>$users
        ],200);
    }

    public function store(Request $request) {
        try{
            $validate = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'phone_number' => 'required|string|digits_between:10,15',
                'address' => 'required|string|max:255',
            ]);
            if ($validate->fails()){
                $response = [
                    'status' => 'error',
                    'data' => $validate->errors(),
                ];
                return response()->json($response,400);
            }

            $users= User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'password' => bcrypt($request->password)
            ]);

            return response()->json([
                'messages' => 'user successfully created',
                'data' => $users,
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'messages' => 'something went wrong',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    public function show(string $id){
        $users = User::find($id);
        if(!$users){
            return response()->json([
                'message' => 'User not found'
            ],404);
        }

        return response()->json([
            'user'=>$users
        ],200);
    }

    public function update(Request $request, string $id) {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }


            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',

                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'phone_number' => 'required|digits_between:10,15',
                'address' => 'required|string|max:255',
                'password' => 'nullable|string|min:8',

            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validate->errors(),
                ], 400);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            return response()->json([
                'user' => $user
            ], 200);

        } catch(\Exception $e){
             return response()->json([
              'messages'=>'something went wrong',
              'error' => $e->getMessage()
            ],500);

        }
    }

    public function destroy(string $id){
        $users = User::find($id);
        if(!$users){
            return response()->json([
                'messages'=>'User not found'
            ],404);
        }

        $users->delete();

        return response()->json([
            'messages'=> 'User sucessfully deleted'
        ],200);
    }
}
