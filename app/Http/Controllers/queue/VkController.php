<?php

namespace App\Http\Controllers\queue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Post;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\InputMedia\InputMediaVideo;

use App\Helpers\TelegramHelper;

class VkController extends Controller
{
    public function vkAnime(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)->whereIn('id', $favorite_ids)->orderBy('created_at', 'desc');
        $name = $request->get('title');
        $category_value = ['anime'];

        if ($name !== null) {
            $objects->where('title', 'like', '%' . $name['searchTerm'] . '%');
        }
        if ($category_value !== null) {
            $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();

            $objects->whereHas('categories', function ($query) use ($category_ids, $request) {
                $query->whereIn('category_id', array_values($category_ids));
            });
        }

        return view('queue.anime', [
            'posts' => $objects->paginate(20)
        ]);
    }
    public function vkSexy(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)->whereIn('id', $favorite_ids)->orderBy('created_at', 'desc');
        $name = $request->get('title');
        $category_value = ['sexy'];

        if ($name !== null) {
            $objects->where('title', 'like', '%' . $name['searchTerm'] . '%');
        }
        if ($category_value !== null) {
            $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();

            $objects->whereHas('categories', function ($query) use ($category_ids, $request) {
                $query->whereIn('category_id', array_values($category_ids));
            });
        }

        return view('queue.sexy', [
            'posts' => $objects->paginate(20)
        ]);
    }
}
