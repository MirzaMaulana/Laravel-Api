<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors(),
            ]);
        }

        try {
            // mencari user berdasarkan emailnya
            $user = User::where('email', $request->email)->firstOrFail();
            //membuatkan token
            $token = $user->createToken('password-reset')->plainTextToken;
            //mengirim pesan ke email user
            $user->sendPasswordResetNotification($token);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Reset password link telah dikirim ke email anda.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required'],
            'current_password' => ['required', 'min:8']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors(),
            ]);
        }
        try {
            $user = auth()->user();
            //mengecek apakah password sebelumnya tidak sama dengan request
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'Failed',
                    'message' => 'Kata sandi saat ini salah, current_password = isi kata sandai saat ini sebelum diubah'
                ], 422);
            }
            //meengupdate data password
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil Mengganti password'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
