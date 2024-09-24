<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index($flag)
    {
        $query = User::select('name','email');

        if($flag == 1){
            $query->where(['status' => 1]);
        }elseif($flag == 0){
            // Empty
        }else{
            $response = [
                'message' => 'Invalid Parameter Passed. It can be 1 or 0',
                'status' => 0
            ];
            return response()->json($response,400);
        }

        $users = $query->get();

        if(count($users) > 0){
            $response = [
                'message' => count($users) . ' users found',
                'status' => 1,
                'data' => $users
            ];
        }else{
            $response = [
                'message' => count($users) . ' users found',
                'status' => 0,
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->messages(),400); // 400 means bad request
        }else{
            DB::beginTransaction();
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];

            try{
                $user = User::create($data);
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                $user = null;
            }

            if($user != null){
                return response()->json([
                    'message' => 'User Successfully Register',
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Internal Server Error'
                ],500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);

        if(is_null($user)){
            $response = [
                'message' => 'User not found.',
                'status' => 0
            ];
        }else{
            $response = [
                'message' => 'User found.',
                'status' => 1,
                'data' => $user
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if(is_null($user)){
            $response = [
                'message' => 'User Does not exists',
                'status' => 0
            ];
            $respCode = 404;
            return response()->json($response, $respCode);
        }else{
            DB::beginTransaction();
            try{
                $user->name = $request->name;
                $user->email = $request->email;
                $user->contact = $request->contact;
                $user->pincode = $request->pincode;
                $user->address = $request->address;
                $user->save();

                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                $user = null;
            }
        }

        if(is_null($user)){
            $response = [
                'message' => 'Internal Server Error',
                'status' => 0
            ];
            $respCode = 500;
        }else{
            $response = [
                'message' => 'User Data Updated Successfully',
                'status' => 1
            ];
            $respCode = 200;
        }

        return response()->json($response, $respCode);
    }

    public function changePassword(Request $request, $id){
        $user = User::find($id);

        if(is_null($user)){
            $response = [
                'message' => 'User Does not exists',
                'status' => 0
            ];
            $respCode = 404;
        }else{
            if($user->password == $request->old_password){
                if($request->new_password == $request->confirm_password){
                    DB::beginTransaction();
                    try{
                        $user->password = $request->new_password;
                        $user->save();
                        DB::commit();
                    }catch(\Exception $e){
                        DB::rollBack();
                        $user = null;
                    }
                    if(is_null($user)){
                        $response = [
                            'message' => 'Internal Server Error',
                            'status' => 0
                        ];
                        $respCode = 500;
                    }else{
                        $response = [
                            'message' => 'User Password Updated Successfully',
                            'status' => 1
                        ];
                        $respCode = 200;
                    }

                }else{
                    $response = [
                        'message' => 'New Password and Confirm Password Does Not Match',
                        'status' => 0
                    ];
                    $respCode = 400;
                }

            }else{
                $response = [
                    'message' => 'Old Password Does Not Match',
                    'status' => 0
                ];
                $respCode = 400;
            }
        }
        return response()->json($response, $respCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(is_null($user)){
            $response = [
                'message' => 'User Does not exists',
                'status' => 0
            ];
            $respCode = 404;
        }else{
            DB::beginTransaction();
            try{
                $user->delete();
                DB::commit();
                $response = [
                    'message' => 'User Deleted Successfully',
                    'status' => 1
                ];
                $respCode = 200;
            }catch(\Exception $e){
                DB::rollBack();
                $response = [
                    'message' => 'Internal Server Error',
                    'status' => 0
                ];
                $respCode = 500;
            }
        }

        return response()->json($response, $respCode);
    }
}