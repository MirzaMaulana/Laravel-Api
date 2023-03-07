<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use Throwable;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // create comment
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'exists:posts,id'],
            'content' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $input = $request->all();
            $input['user_id'] = auth()->id();
            $comment = Comment::create($input);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Sukses Mengupload Comment',
                'data' =>  new CommentResource($comment)
            ]);
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
            'content' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $findComment = Comment::findOrFail($id);
            $findComment->update($request->only('content'));
            return response()->json([
                'status' => 'Sukses',
                'message' => 'Sukses Mengupdate Comment',
                'data' =>   new CommentResource($findComment)
            ]);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Komentar Sukses Dihapus'
            ]);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
}
