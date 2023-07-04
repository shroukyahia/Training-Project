<?php

namespace App\Http\Controllers\api\user;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show', 'showLatestPosts']);
    }

    public function index()
    {
        $posts = Post::with('user')->select('id', 'title', 'body', 'user_id')->paginate(5);
        $response = [
            'message' => 'All posts that have been created.',
            'result' => $posts,
        ];
        return response($response, 201);
    }
    public function store(Request $request)
    {
        $user = User::find($request->user()->id);
        $user_name = $user->name;
        $request->validate(
            [
                'title' => 'required|string',
                'body' => 'required|string',
            ]
        );
        $file = $request->file('file');
        if ($file) {
            $image_path = Storage::putFile('posts', $file);
        } else {
            $image_path = null;
        }
        $post = Post::create(
            [
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'body' => $request->body,
                'file' => $image_path,
                'created_at' => Carbon::now(),
            ]
        );
        $response = [
            'message' => 'Your post created successfully.',
            'author' => $user_name,
            'result' => $post,
        ];
        return response($response, 201);
    }

    public function show(string $id)
    {
        $post = Post::with('user')->select('id', 'title', 'body', 'user_id')->where('id', $id)->get();
        $response = [
            'message' => 'show specific post.',
            'result' => $post,
        ];
        return response($response, 201);
    }
    public function showLatestPosts()
    {
        $posts = Post::with('user')->select('id', 'title', 'body', 'user_id')->latest()->take(5)->get();
        $response = [
            'message' => 'show latest posts.',
            'result' => $posts,
        ];
        return response($response, 201);
    }

    public function update(Request $request,  $id)
    {
        $post = Post::find($id);
        if ($request->user()->id == $post->user_id) {
            $request->validate(
                [
                    'title' => 'required|string',
                    'body' => 'required|string',
                ]
            );
            $file = $request->file('file');
            if ($file) {
                $image_path = Storage::putFile('posts', $file);
            } else {
                $image_path = null;
            }
            $post->update(
                [
                    'user_id' => $request->user()->id,
                    'title' => $request->title,
                    'body' => $request->body,
                    'file' => $image_path,
                    'updated_at' => Carbon::now(),
                ]
            );
            $response = [
                'message' => 'Your post updated successfully.',
                'result' => $post,
            ];
        } else {
            $response = [
                'message' => "You can't update this post only owner of the post can update it.",
            ];
        }

        return response($response, 201);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id == $request->user()->id) {
            $post->delete();
            $response = [
                'message' => "The post is deleted successfully.",
            ];
        } else {
            $response = [
                'message' => "You can't delete this post.",
            ];
        }
        return response($response, 201);
    }
}