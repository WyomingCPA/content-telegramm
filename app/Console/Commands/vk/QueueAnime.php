<?php

namespace App\Console\Commands\vk;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Views;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;

class QueueAnime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:publish-anime-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //Сделать проверку запуска публикаций для телеграмм
        $isStart = Group::where('slug', '=', 'anime')->first();
        if (!$isStart->is_start) {
            echo "Не публикуем";
            return Command::SUCCESS;
        }

        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $favorite_ids = $user->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)->whereIn('id', $favorite_ids)->orderBy('created_at', 'desc');

        $category_value = ['anime'];
        $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();
        $objects->whereHas('categories', function ($query) use ($category_ids) {
            $query->whereIn('category_id', array_values($category_ids));
        });

        if ($objects->count() == 0) {
            $objects =  Post::where('is_publish', true)->where('is_hidden', false)->orderBy('updated_at', 'asc');
            $category_value = ['anime'];
            $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();
            $objects->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('category_id', array_values($category_ids));
            });
            //echo $objects->count();
        }

        $post = $objects->inRandomOrder()->first();
        echo $post->id;
        $post->touch();

        try {
            //Telegramm
            $messageText = '';
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

            echo 'Публикация выполена успешно';
        } catch (\Error $e) {
            $user->queuesPost()->detach(array_values([$post->id]));
            $post->is_hidden = true;
            $post->save();
            echo $e->getMessage();
        }
    }
}
