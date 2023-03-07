<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Post $post)
    {
        $posts = Post::all();
        return PostResource::collection($posts);
    }
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $post->views++;
        $post->save();
        return response()->json([
            'status' => 'Sukses',
            'message' => 'Sukses Mendapatkan data post',
            'data' => new PostResource($post),
        ], 201);
    }
    public function store(Request $request)
    {
        // memvalidasi inputan post
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
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
                'title' => $request->title,
                'content' => $request->content,
                'created_by' => Auth::user()->name
            ];

            $post = Post::create($input);
            $success = $post;

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil membuat post baru',
                'data' => [
                    'post' => PostResource::collection($success),
                ],
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Sukses',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // memvalidasi inputan post
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
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
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil mengupdate post',
                'data' => [
                    'post' => $post,
                ],
            ], 200);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy(Post $post)
    {
        try {
            $post->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'sukses menghapus post'
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
