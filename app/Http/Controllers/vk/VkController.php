<?php

namespace App\Http\Controllers\vk;

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
    public function vkAnimeAll(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->whereJsonLength('attachments', '>', 0)
            ->whereNotIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');

        $category_value = ['anime'];
        if ($category_value !== null) {
            $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();

            $objects->whereHas('categories', function ($query) use ($category_ids, $request) {
                $query->whereIn('category_id', array_values($category_ids));
            });
        }
        return view('vk.anime-photo', [
            'posts' => $objects->paginate(50)
        ]);
    }
    public function sexyPhotoAll(Request $request)
    {
        $favorite_ids = Auth::user()->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->whereNotIn('id', $favorite_ids)->whereJsonLength('attachments', '>', 0)
            ->orderBy('created_at', 'desc');
        //$test = Post::whereJsonLength('attachments', '>', 0)->get();
        $categories = Category::pluck('name')->toArray();
        $count = $objects->count();
        $sort = $request->get('sort');
        $direction = $request->get('direction');
        $name = $request->get('title');
        $category_value = ['sexy'];
        $created_by = $request->get('created_by');
        $type = $request->get('type');
        $limit = 50;
        $page = (int) $request->get('page');
        $created_at = $request->get('created_at');

        if ($name !== null) {
            $objects->where('title', 'like', '%' . $name['searchTerm'] . '%');
        }
        if ($category_value !== null) {
            $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();

            $objects->whereHas('categories', function ($query) use ($category_ids, $request) {
                $query->whereIn('category_id', array_values($category_ids));
            });
        }
        $objects->offset($limit * ($page - 1))->limit($limit);
        //$test = $objects->first()->attachments;
        //foreach ($test as $item_test)
        //{
        //    echo "break";
        //}
        return view('vk.sexy-photo', [
            'posts' => $objects->paginate(50)
        ]);
    }
    public function vkAnimePublish(Request $request)
    {
        $rows = $request->ids;
        $select = [];
        foreach ($rows as $value) {
            $messageText = '';
            //$select[] = $value['id'];
            $post = Post::findOrFail($value);
            $categories = $post->categories;
            $list_img = $post->attachments;
            $tags = '#art #tyan ';
            foreach ($categories as $item_category) {
                $tags .= "#" . $item_category->name . " ";
            }
            $messageText .= "\n";
            $messageText .= $tags;
            if (!empty($messageText)) {
                $chatId = '-1001771871700';
                //$chatId = '-414528593';
                $bot = new BotApi(env('TELEGRAM_TOKEN'));
                //$bot->sendMessage($chatId, $messageText, 'HTML');

                $media = new ArrayOfInputMedia();
                foreach ($list_img as $img) {
                    if (count($list_img) != 1) {
                        $image = end($img);
                        $messageText = " #anime #art #tyan \n\n\n<a href='https://t.me/+ATd62K2jKB43YzIy'>Anime_Tyn_TG</a>";
                        $media->addItem(new InputMediaPhoto($image, $messageText, 'HTML'));
                    } else {
                        $item_image = end($img);

                        $messageText = " #anime #art #tyan \n\n\n<a href='https://t.me/+ATd62K2jKB43YzIy'>Anime_Tyn_TG</a>";
                        $media->addItem(new InputMediaPhoto($item_image, $messageText, 'HTML'));
                    }
                }
                $bot->sendMediaGroup($chatId, $media);
                $post->is_publish = true;
                $post->save();
            }
        }
        //return redirect()->route('vk.anime-photo-all', );
    }
    public function vkSexyPublish(Request $request)
    {
        $rows = $request->ids;
        $select = [];
        foreach ($rows as $value) {
            $messageText = '';
            //$select[] = $value['id'];
            $post = Post::findOrFail($value);
            $categories = $post->categories;
            $list_img = $post->attachments;
            $tags = '#girl #body #fit';
            foreach ($categories as $item_category) {
                $tags .= "#" . $item_category->name . " ";
            }
            $messageText .= "\n";
            $messageText .= $tags;
            if (!empty($messageText)) {
                $chatId = '-1002366645779';
                //$chatId = '-414528593';
                $bot = new BotApi(env('TELEGRAM_TOKEN'));
                //$bot->sendMessage($chatId, $messageText, 'HTML');

                $media = new ArrayOfInputMedia();
                foreach ($list_img as $img) {
                    if (count($list_img) != 1) {
                        $image = end($img);
                        $messageText = "#girl #body #fit \n\n\n<a href='https://t.me/+U0H_PQ6A29g0ZmVi'>Bikini Paradise</a>";
                        $media->addItem(new InputMediaPhoto($image, $messageText, 'HTML'));
                    } else {
                        $item_image = end($img);
                        $messageText = "#girl #body #fit \n\n\n<a href='https://t.me/+U0H_PQ6A29g0ZmVi'>Bikini Paradise</a>";
                        $media->addItem(new InputMediaPhoto($item_image, $messageText, 'HTML'));
                    }
                }

                $bot->sendMediaGroup($chatId, $media);
                $post->is_publish = true;
                $post->save();
            }
        }
    }
    public function postHidden(Request $request)
    {
        $rows = $request->ids;
        $select = [];
        foreach ($rows as $value) {
            $post = Post::findOrFail($value);
            $post->is_hidden = true;
            $post->save();
        }
    }
}
