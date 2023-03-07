<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MyProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json($user);
    }

    public function update(Request $request)
    {
        try {
            // Mem validasi inputan update
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string', 'max:255'],
                    'alamat' => ['required', 'string', 'max:255'],
                    'jenis_kelamin' => ['required']
                ],
                [
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
                //setelah validasi berhasil maka akan menjalankan program ini
                $user = $request->user();
                if (!$user) {
                    return response()->json([
                        'message' => 'Pengguna tidak ditemukan',
                    ], 404);
                }
                // ?menerima inputan
                $input = $request->all();
                //menyimpan inputan
                $user->update($input);
                //mengembalikan nilai inputan jika berhasil
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
