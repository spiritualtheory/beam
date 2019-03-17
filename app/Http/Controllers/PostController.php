<?php

namespace App\Http\Controllers;

use App\Beam;
use Illuminate\Http\Request;
use App\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all()->where('parent_id', null)->sortByDesc('created_at');
        $comments = Post::all()->where('parent_id', !null)->sortByDesc('created_at');
        return view('posts', ['posts' => $posts, 'comments' => $comments]);
    }

    public function save(Request $request)
    {
        Post::create([
            'user_id' => $request->user()->id,
            'message'=> request('message'),
            'created_at' => gmdate("Y-m-d\TH:i:s\Z"),
        ]);

        $beams = Beam::all();
        $client = new \GuzzleHttp\Client();

        foreach ($beams as $beam) {
            $response = $client->request('POST', $beam->url . '/api/xposts/1/save', [
                'form_params' => [
                    'user_id' => $request->user()->id,
                    'message' => request('message'),
                    'beam_id' => $beam->id,
                 ]
            ]);
        }

        return redirect('posts');
    }

    public function comment(Request $request)
    {
        Post::create([
            'user_id' => $request->user()->id,
            'message'=> request('message'),
            'parent_id' => intval(request('parent_id')),
            'created_at' => gmdate("Y-m-d\TH:i:s\Z"),
        ]);

        return redirect('posts');
    }
}
