<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function index()
    {
        $postType = '文章總覽';

        $posts = Post::orderBy('updated_at','ASC')->paginate(5);
        $data = compact('postType','posts');
        /*$posts = \App\Post::orderBy('created_at', 'desc')
                          ->paginate(5);

        $data = compact('postType', 'posts');*/

        return view('posts.index', $data);
    }

    public function hot()
    {
        $postType = '熱門文章';

        $posts = \App\Post::where('page_view', '>=', 100)
                          ->orderBy('page_view', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(5);

        $data = compact('postType', 'posts');

        return view('posts.index', $data);
    }

    public function random()
    {
        $post = \App\Post::all()->random();

        $data = compact('post');

        return view('posts.show', $data);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $post = \App\Post::create($request->all());
        $data = compact('post');
        

        //v1,ok
        //return redirect()->route('posts.create');
        //v2,ok
        return redirect()->route('posts.show',$data);

        //old
        //return redirect()->route('posts.show', $post->id);
    }

    public function show($id)
    {
        $post = \App\Post::find($id);

        $post->page_view += 1;
        $post->save();

        $data = compact('post');

        return view('posts.show', $data);
    }

    public function edit($id)
    {
        $post = \App\Post::find($id);

        $data = compact('post');

        return view('posts.edit', $data);
    }

    public function update($id, Request $request)
    {
        $post = Post::find($id);        
        $post->update($request->except('_token'));
        return redirect()->route('posts.edit',$post->id);
               
        //old//$post->update($request->all());
        //old//        return redirect()->route('posts.show', $post->id);
    }

    public function destroy($id)
    {
        
        Post::destroy($id);
        return redirect()->route('posts.index');


        /*
        v2,ok
        $post = Post::find($id);
        $post->delete();
        v1,ok
        Post::destroy($id);*/

        /*$post = \App\Post::find($id);
        foreach($post->comments as $comment) {
            $comment->delete();
        }

        $post->delete();

        return redirect()->route('posts.index');*/
    }

    public function comment($id, Request $request)
    {
        $post    = \App\Post::find($id);
        $comment = \App\Comment::create($request->all());

        $post->comments()->save($comment);

        return redirect()->route('posts.show', $post->id);
    }
}
