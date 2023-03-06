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
                //setelah validasi berhasil maka akan menjalankan program ini
                $findUser = User::find($request->user()->id);
                if (!$findUser) {
                    return response()->json([
                        'message' => 'Pengguna tidak ditemukan',
                    ], 404);
                }
                //menyimpan request
                $findUser->name = $request->name;
                $findUser->save();
                $success['name'] = $findUser->name;
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
