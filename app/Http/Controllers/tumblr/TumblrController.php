<?php

namespace App\Http\Controllers\tumblr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use TelegramBot\Api\Types\InputMedia\InputMediaVideo;
use \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;

class TumblrController extends Controller
{
    public function sexyPhotoAll(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'tumblr')
            ->where('type', 'photo')
            ->where('owner_id', 213)
            ->whereNotIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');


        return view('tumblr.sexy-photo', [
            'posts' => $objects->paginate(20)
        ]);
    }

    public function sexyVideoAll(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'tumblr')
            ->where('type', 'video')
            ->where('owner_id', 213)
            ->whereNotIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');

        return view('tumblr.sexy-video', [
            'posts' => $objects->paginate(20)
        ]);
    }

    public function animePhotoAll(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'tumblr')
            ->where('type', 'photo')
            ->where('owner_id', 313)
            ->whereNotIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');

        return view('tumblr.anime-photo', [
            'posts' => $objects->paginate(20)
        ]);
    }

    public function animeVideoAll(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'tumblr')
            ->where('type', 'video')
            ->where('owner_id', 313)
            ->whereNotIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');

        return view('tumblr.anime-video', [
            'posts' => $objects->paginate(20)
        ]);
    }
}
