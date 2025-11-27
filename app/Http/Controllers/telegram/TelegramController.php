<?php

namespace App\Http\Controllers\telegram;

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

class TelegramController extends Controller
{
    public function animePhotoAll(Request $request)
    {
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'telegramm')
            ->where('type', 'photo')
            ->where('owner_id', 313)
            //->whereNotIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');

        return view('telegram.anime-photo', [
            'posts' => $objects->paginate(20)
        ]);
    }
    public function sexyPhotoAll(Request $request)
    {
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'telegramm')
            ->where('type', 'photo')
            ->where('owner_id', 213)
            ->orderBy('created_at', 'desc');

        return view('telegram.sexy-photo', [
            'posts' => $objects->paginate(20)
        ]);
    }

    public function sexyVideoAll(Request $request)
    {
        $objects = Post::where('is_publish', false)->where('is_hidden', false)
            ->where('network', 'telegramm')
            ->where('type', 'video')
            ->where('owner_id', 213)
            ->orderBy('created_at', 'desc');

        return view('telegram.sexy-video', [
            'posts' => $objects->paginate(20)
        ]);
    }

    public function sexyVideoPublish(Request $request)
    {
        $telegram = new TelegramHelper();

        $rows = $request->ids;

        if (!$rows) {
            return back()->with('error', 'Ничего не выбрано.');
        }

        $select = [];
        foreach ($rows as $value) {
            $messageText = '';
            //$select[] = $value['id'];
            $post = Post::findOrFail($value);
            $categories = $post->categories;
            $video = $post->attachments;
            $tags = '';
            foreach ($categories as $item_category) {
                $tags .= "#" . $item_category->name . " ";
            }
            $messageText .= "\n";
            $messageText .= $post->text;
            if (!empty($messageText)) {
                $chatId = '-1002366645779';
                //$chatId = '-414528593';
                $bot = new BotApi(env('TELEGRAM_TOKEN'));
                //$bot->sendMessage($chatId, $messageText, 'HTML');

                $media = new ArrayOfInputMedia();
                $messageText .= " #girl #body #fit \n\n\n<a href='https://t.me/+U0H_PQ6A29g0ZmVi'>Bikini Paradise</a>";

                //$media->addItem(new InputMediaVideo($video[1], $messageText, 'HTML'));

                //$bot->sendMediaGroup($chatId, $media);

                $telegram->sendVideos($chatId, $video[1], $messageText, 'HTML');
                $post->is_publish = true;
                $post->save();
            }
        }
        
        return back()->with('success', 'Посты опубликованы.');
    }

    public function sexyPhotoPublish(Request $request)
    {
        $telegram = new TelegramHelper();
        $rows = $request->ids;
        $select = [];
        foreach ($rows as $value) {
            $messageText = '';
            //$select[] = $value['id'];
            $post = Post::findOrFail($value);
            $categories = $post->categories;
            $list_img = $post->attachments;
            $tags = '';
            foreach ($categories as $item_category) {
                $tags .= "#" . $item_category->name . " ";
            }
            $messageText .= "\n";
            $messageText .= $post->text;
            if (!empty($messageText)) {
                $chatId = '-1002366645779';
                //$chatId = '-414528593';
                $bot = new BotApi(env('TELEGRAM_TOKEN'));
                //$bot->sendMessage($chatId, $messageText, 'HTML');

                $media = new ArrayOfInputMedia();
                $messageText .= " #girl #body #fit \n\n\n<a href='https://t.me/+U0H_PQ6A29g0ZmVi'>Bikini Paradise</a>";

                if (is_array($list_img[1])) {
                    $imgUrls = [];
                    foreach ($list_img[1] as $item_image) {
                        $imgUrls[] = $item_image;
                    }
                    // Отправка нескольких картинок
                    $telegram->sendPhotos($chatId, $imgUrls, $messageText, 'HTML');
                    $post->is_publish = true;
                    $post->save();
                } else {

                    $telegram = new TelegramHelper();
                    $telegram->sendPhotos($chatId, $list_img[1], $messageText, 'HTML');
                    $post->is_publish = true;
                    $post->save();
                }
            }
        }
        return back()->with('success', 'Посты опубликованы.');
    }
    public function animePhotoPublish(Request $request)
    {
        $telegram = new TelegramHelper();

        $rows = $request->ids;

        if (!$rows) {
            return back()->with('error', 'Ничего не выбрано.');
        }

        $select = [];
        foreach ($rows as $value) {
            $messageText = '';
            //$select[] = $value['id'];
            $post = Post::findOrFail($value);
            $categories = $post->categories;
            $list_img = $post->attachments;
            $tags = '';
            foreach ($categories as $item_category) {
                $tags .= "#" . $item_category->name . " ";
            }
            $messageText .= "\n";
            $messageText .= $post->text;
            if (!empty($messageText)) {
                $chatId = '-1001771871700';
                //$chatId = '-414528593';
                $bot = new BotApi(env('TELEGRAM_TOKEN'));
                //$bot->sendMessage($chatId, $messageText, 'HTML');

                $media = new ArrayOfInputMedia();
                $messageText = " #anime #art #tyan \n\n\n<a href='https://t.me/+ATd62K2jKB43YzIy'>Anime_Tyn_TG</a>";

                if (is_array($list_img[1])) {
                    $imgUrls = [];
                    foreach ($list_img[1] as $item_image) {
                        //$media->addItem(new InputMediaPhoto($item_image, $messageText, 'HTML'));
                        $imgUrls[] = $item_image;
                    }
                    // Отправка нескольких картинок
                    $telegram->sendPhotos($chatId, $imgUrls, $messageText, 'HTML');
                    $post->is_publish = true;
                    $post->save();
                } else {
                    //Скачиваем картинку                  
                    //$media->addItem(new InputMediaPhoto($list_img[1], $messageText, 'HTML'));
                    $telegram = new TelegramHelper();
                    $telegram->sendPhotos($chatId, $list_img[1], $messageText, 'HTML');
                    $post->is_publish = true;
                    $post->save();
                }
            }
        }

        return back()->with('success', 'Посты опубликованы.');
    }
}
