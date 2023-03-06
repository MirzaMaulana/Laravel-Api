<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //memvalidasi inputan register
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:password']
        ]);
        //mengecek ketika terjadi error saat input data
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data Yang Anda Berikan Tidak Valid',
                'errors' => $validator->errors(),
            ], 422);
        }
        //menyimpan data
        try {
            $input = $request->all();
            $input['password'] = Hash::make('password');
            $user = User::create($input);

            //memberikan token
            $token = $user->createToken('api_token')->plainTextToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            return response()->json([
                'message' => 'Anda Berhasil Register',
                'data' => [
                    'user' => $success,
                    'api_token' => $token,
                ],
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    //function login
    public function login(Request $request)
    {
        //memvalidasi input login
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        //mengecek jika user telah register dan berhasil login
        if (Auth::attempt($data)) {
            $auth = Auth::user();
            $token = $auth->createToken('api_token')->plainTextToken;
            $success['name'] = $auth->name;
            $success['email'] = $auth->email;

            return response()->json([
                'message' => 'Anda Berhasil login',
                'data' => [
                    'user' => $success,
                    'api_token' => $token,
                ],
            ], 200);
            return response()->json([
                'message' => 'Anda Berhasil login',
                'data' => [
                    'user' => $success,
                    'api_token' => $token,
                ],
            ], 200);
        } else {
            //jika salah login
            return response()->json([
                'message' => 'Terjadi Kesalahan Cek Email Dan Password',
            ], 422);
        }
    }
}
