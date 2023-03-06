<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Membuat crud member
    public function store(Request $request)
    {
        //memvalidasi inputan 
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
        try {
            $input = $request->all();
            $input['password'] = Hash::make('password');
            $user = User::create($input);
            $success = $user;

            return response()->json([
                'message' => 'Berhasil Membuat User Baru',
                'data' => [
                    'user' => $success
                ],
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function show()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'message' => 'sukses menghapus user'
            ], 200);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            // Memvalidasi inputan update
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
            ]);
            // jika kondisi validasi tidak sesuai maka akan muncul pesan ini
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Data yang anda berikan tidak valid',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                if (!$user) {
                    return response()->json([
                        'message' => 'User tidak ditemukan',
                    ], 404);
                }
                //menyimpan request
                $user->name = $request->name;
                $user->save();
                $success['name'] = $user->name;
                return response()->json([
                    'message' => 'Sukses Mengupdate data profile',
                    'data' => [
                        'user' => $success,
                    ],
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi',
            ], 500);
        }
    }
}
