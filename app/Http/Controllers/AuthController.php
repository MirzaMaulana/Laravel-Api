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
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:password']
        ], [
            'name.required' => 'name harus di isi',
            'name.string' => 'name harus bernilai string',
            'email.required' => 'email harus di isi',
            'email.email' => 'Format email salah, seharusnya contoh@example.com',
            'password.required' => 'password harus di isi',
            'password.min:8' => 'password minimal 8 huruf',
            'confirm_password.required' => 'confirmasi password harus di isi',
            'confirm_password.same:password' => 'confirmasi password salah pastikan confirmasi password sama dengan password'
        ]);
        //mengecek ketika terjadi error saat input data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
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
            $token = $user->createToken('token')->plainTextToken;
            Auth::login($user);
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            return response()->json([
                'status' => 'Sukses',
                'message' => 'Anda Berhasil Register dan login',
                'token' => $token,
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
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
        ], [
            'email.required' => 'email harus di isi',
            'email.email' => 'Format email salah, seharusnya contoh@example.com',
            'password.required' => 'password harus di isi',
            'password.min:8' => 'password minimal 8 huruf',
        ]);

        //mengecek jika user telah register dan berhasil login
        if (Auth::attempt($data)) {
            Auth::attempt($data, $remember = true);
            $auth = Auth::user();
            $token = $auth->createToken('token')->plainTextToken;
            $success['name'] = $auth->name;
            $success['email'] = $auth->email;

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Anda Berhasil login',
                'token' => $token,
            ], 200);
        } else {
            //jika salah login
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Cek Email Dan Password',
            ], 422);
        }
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'Anda Berhasil Logout',
            ], 200);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
}
