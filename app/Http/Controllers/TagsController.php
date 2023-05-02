<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Tags;
use Illuminate\Http\Request;
use App\Http\Resources\TagsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{
    public function index()
    {
        $tags = Tags::latest()->get();
        return response()->json([
            'status' => 'Sukses',
            'data' => TagsResource::collection($tags),
        ], 201);
    }
    public function store(Request $request)
    {
        // memvalidasi inputan post
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'name tidak boleh kosong',
            'name.string' => 'name harus bernilai string'
        ]);

        // mengecek ketika terjadi error saat input data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $input = [
                'name' => $request->name,
                'created_by' => Auth::user()->name
            ];

            $tag = Tags::create($input);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil membuat Tag baru',
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'name tidak boleh kosong',
            'name.string' => 'name harus bernilai string'
        ]);

        // mengecek ketika terjadi error saat input data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = Tags::find($id);
            $tag->name = $request->name;
            $tag->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil Mengedit Tag',
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy(Tags $tag)
    {
        try {
            $tag->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'sukses menghapus tag'
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
