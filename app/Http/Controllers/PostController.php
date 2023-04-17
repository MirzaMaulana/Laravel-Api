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
    public function index(Request $request, Post $post)
    {
        $tag = $request->query('tag');
        $posts = $tag ? $post->whereHas('tag', function ($query) use ($tag) {
            $query->where('name', $tag);
        })->paginate(10) : $post->paginate(10);

        $postsData = $posts->items();
        $pinned = Post::Where('is_pinned', 1)->get();
        $nextPageUrl = $posts->nextPageUrl();
        $prevPageUrl = $posts->previousPageUrl();
        $response = [
            'message' => 'Menampilkan Semua Users',
            'data' => PostResource::collection($postsData),
            'pinned' => $pinned
        ];

        if (!is_null($nextPageUrl)) {
            $response['selanjutnya'] = $nextPageUrl;
        }

        if (!is_null($prevPageUrl)) {
            $response['sebelumnya'] = $prevPageUrl;
        }

        return response()->json($response);
    }
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $post->views++;
        $post->update();
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
            'tag' => ['required']
        ], [
            'title.required' => 'title tidak boleh kosong',
            'content.required' => 'content tidak boleh kosong',
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
                'is_pinned' => $request->is_pinned,
                'created_by' => Auth::user()->name
            ];

            if ($request->hasFile('image')) {
                $filename = $request->image->getClientOriginalName();
                $request->image->storeAs('public/posts', $filename);
                $input['image'] = asset('storage/posts/' . $filename);;
            }

            $post = Post::create($input);
            $post->tag()->attach($request->tag);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil membuat post baru',
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
        // memvalidasi inputan post
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
        ], [
            'title' => 'title tidak boleh kosong',
            'content' => 'content tidak boleh kosong'
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
            $post->is_pinned = $request->is_pinned;
            $post->tag()->sync($request->tag);
            $post->save();


            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil mengupdate post',
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
