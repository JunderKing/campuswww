<?php

namespace App\Http\Controllers\Campus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class PostController extends Controller
{
    public function addPost(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'timeId' => 'required|numeric',
            'imageNum' => 'required|numeric',
            'content' => 'required|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        if ($imageNum === 0 || !($request->file('postImage')->isValid())) {
            $postObj = Model\Post::create([
                'user_id' => $userId,
                'time_id' => $timeId,
                'content' => $content
            ]);
            return $this->output(['postId' => $postObj->post_id]);
        }
        $fileName = "post-$imageNum-" . time() . ".png";
        $result = $request->file('postImage')->storeAs('postImage', $fileName, 'public');
        $postObj = Model\Post::updateOrCreate(
            ['user_id' => $userId, 'time_id' => $timeId],
            ['content' => $content]
        );
        $postIdArr = Model\Post::where([['user_id', $userId], ['time_id', $timeId]])
            ->pluck('post_id')->toArray();
        $postId = $postIdArr[0];
        $result = Model\PostImage::updateOrCreate(
            ['post_id' => $postId, 'image_num' => $imageNum],
            ['image_url' => "https://www.kingco.tech/storage/postImage/$fileName"]
        );
        return $this->output(['postId' => $postId]);
    }

    public function delPost(Request $request)
    {
        $params = $this->validation($request, [
            'postId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\Post::where('post_id', $postId)->delete();
        Model\PostImage::where('post_id', $postId)->delete();
        return $this->output(['deleted' => $result]);
    }

    public function getPostInfo(Request $request)
    {
        $params = $this->validation($request, [
            'postId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $postInfo = Model\Post::where('post_id', $postId)
            ->join('user', 'post.user_id', '=', 'user.user_id')
            ->select('user.user_id', 'user.avatar_url', 'user.nick_name', 'post.content', 'post.created_at')
            ->first()->toArray();
        $images = Model\PostImage::where('post_id', $postId)
            ->orderBy('image_num', 'asc')
            ->pluck('image_url')->toArray();
        $postInfo['images'] = $images;
        return $this->output(['postInfo' => $postInfo]);
    }

    public function getPostList(Request $request)
    {
        //$params = $this->validation($request, [
        //'postId' => 'required|numeric'
        //]);
        //if ($params === false) {
        //return self::$ERROR1;
        //}
        //extract($params);
        $postList = Model\Post::join('user', 'post.user_id', '=', 'user.user_id')
            ->orderBy('created_at', 'desc')
            ->select('user.user_id', 'user.avatar_url', 'user.nick_name', 'post.post_id', 'post.content', 'post.created_at')
            ->get()->toArray();
        foreach ($postList as &$postInfo) {
            $postInfo['images'] = Model\PostImage::where('post_id', $postInfo['post_id'])
                ->orderBy('image_num', 'asc')
                ->pluck('image_url')->toArray();
        }
        return $this->output(['postList' => $postList]);
    }
}
