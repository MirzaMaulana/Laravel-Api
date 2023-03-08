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
        try {
            $input = $request->all();
            $input['role'] = 'Admin';
            $input['password'] = Hash::make('password');
            $user = User::create($input);
            $success = $user;

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil Membuat User Baru',
                'data' => [
                    'user' => $success
                ],
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function show()
    {
        $users = User::all();
        return response()->json([
            'status' => 'Sukses',
            'message' => 'Sukses mendapatkan data',
            'data' => UserResource::collection($users)
        ], 200);
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'sukses menghapus user'
            ], 200);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            // Memvalidasi inputan update
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string', 'max:255'],
                    'alamat' => ['required', 'string', 'max:255'],
                    'jenis_kelamin' => ['required']
                ],
                [
                    'name.required' => 'name harus di isi',
                    'name.string' => 'name harus bernilai string',
                    'alamat' => 'alamat tidak boleh kosong',
                    'jenis_kelamin' => 'Kalo Gak Laki-Laki ya Perempuan, Jangan pilih yang lain!'
                ]
            );
            // jika kondisi validasi tidak sesuai maka akan muncul pesan ini
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'Failed',
                    'message' => 'Data yang anda berikan tidak valid',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                if (!$user) {
                    return response()->json([
                        'status' => 'Failed',
                        'message' => 'User tidak ditemukan',
                    ], 404);
                }
                //menyimpan request
                $input = $request->all();
                $user->update($input);
                return response()->json([
                    'status' => 'Sukses',
                    'message' => 'Sukses Mengupdate data profile',
                    'data' => [
                        'user' => $user,
                    ],
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi',
            ], 500);
        }
    }
}
