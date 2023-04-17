<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users'],
        ], [
            'email.required' => 'email harus di isi',
            'email.email' => 'Format email salah, seharusnya contoh@example.com',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors(),
            ]);
        }

        try {
            // mencari user berdasarkan emailnya
            $user = User::where('remember_token', $request->remember_token)->firstOrFail();
            $token = Str::random(20); //memberikan token random ke colom remember_token
            $user->forceFill([
                'remember_token' => $token //menyimpan token
            ])->save();
            $token = $user->remember_token;
            $user->sendPasswordResetNotification($token); //mengirim pesan
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
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required'],
                'token' => ['required'],
                'password' => ['required'],
                'confirm_password' => ['required', 'same:password']
            ],
            [
                'token' => 'isi token untuk merubah password',
                'password.required' => 'password harus di isi',
                'confirm_password.required' => 'confirmasi password harus di isi',
                'confirm_password.same:password' => 'confirmasi password salah pastikan confirmasi password sama dengan password'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors(),
            ]);
        }
        try {
            $user = User::where('email', $request->email)->first();
            //input token 
            $resetPasswordToken = $request->input('token');
            //ambil dari colom remember_token
            $sessionToken = User::where('remember_token', $resetPasswordToken)->first();
            //jika token berbeda dengan nilai di kolom remember_token maka tampil ini
            if (!$sessionToken) {
                return response()->json([
                    'status' => 'Failed',
                    'message' => 'Token reset password tidak valid atau kadaluarsa. Silakan melakukan reset password kembali.'
                ], 422);
            }
            //meengupdate data password
            $user->password = Hash::make($request->password);
            $user->remember_token = null; // Set nilai remember_token ke null
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
