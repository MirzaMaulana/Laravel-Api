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
    public function create(Request $request, $postId)
    {
        $validator = Validator::make($request->all(), [
            'content' => ['required'],
        ], [
            'content.required' => 'komentar tidak boleh kosong'
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
            $input['post_id'] = $postId;
            $comment = Comment::create($input);
            return response()->json([
                'status' => 'Sukses',
                'message' => 'Sukses Mengupload Comment',
            ]);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function reply(Request $request, Comment $comment)
    {

        $comment = Comment::findOrFail($request->input('comment_id'));

        $validator = Validator::make($request->all(), [
            'content' => ['required'],
        ], [
            'content.required' => 'komentar tidak boleh kosong'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reply = new Comment;
            $reply->post_id = $request->input('post_id');
            $reply->content = $request->input('content');
            $reply->user_id = auth()->user()->id;
            $comment->replies()->save($reply);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Sukses Membalas Comment',
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
        ], [
            'content.required' => 'komentar tidak boleh kosong'
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
