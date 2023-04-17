<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
        $Category = Category::all();
        return response()->json([
            'status' => 'Sukses',
            'data' => CategoryResource::collection($Category),
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

            $category = Category::create($input);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil membuat category baru',
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
            $category = Category::find($id);
            $category->name = $request->name;
            $category->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil Mengedit category',
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'sukses menghapus category'
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
