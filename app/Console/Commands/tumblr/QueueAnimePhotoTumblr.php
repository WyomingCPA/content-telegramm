<?php

namespace App\Console\Commands\tumblr;

use Illuminate\Console\Command;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Views;


use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\InputMedia\InputMediaVideo;

class QueueAnimePhotoTumblr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:queue-anime-photo-tumblr';

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
        $proxy_env = env('SERVER_PROXY');
        $proxy_password_env = env('PROXY_PASSWORD');

        //Сделать проверку запуска публикаций для телеграмм
        $isStart = Group::where('slug', '=', 'anime')->first();
        if (!$isStart->is_start) {
            echo "Не публикуем";
            return Command::SUCCESS;
        }
        // проверка лимита
        if ($isStart->posts_today >= 25) {
            echo "Лимит публикаций достигнут (25)";
            return Command::SUCCESS;
        }

        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $favorite_ids = $user->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)
            ->where('owner_id', 313)
            ->where('type', 'photo')
            ->where('network', 'tumblr')
            ->where('is_hidden', false)
            ->whereIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc');

        echo $objects->count();
        $post = $objects->inRandomOrder()->first();
        echo $post->id;
        $post->touch();

        try {
            $messageText = '';
            //$select[] = $value['id'];
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
                $bot->setCurlOption(CURLOPT_TIMEOUT, 0);
                // Настройка CURL для использования SOCKS5 с авторизацией
                $bot->setCurlOption(CURLOPT_PROXY, $proxy_env); //'127.0.0.1:27504'
                //$this->bot->setCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
                $bot->setCurlOption(CURLOPT_PROXYUSERPWD, $proxy_password_env);
                
                $media = new ArrayOfInputMedia();
                $messageText .= " #anime #art #tyan \n\n\n<a href='https://t.me/+ATd62K2jKB43YzIy'>Anime_Tyn_TG</a>";

                foreach ($list_img[1] as $item_image) {
                    $media->addItem(new InputMediaPhoto($item_image, $messageText, 'HTML'));
                }

                $bot->sendMediaGroup($chatId, $media);
                $post->is_publish = true;
                $post->save();

                $isStart->increment('posts_count');
            }
        } catch (\Error $e) {
            $user->queuesPost()->detach(array_values([$post->id]));
            $post->is_hidden = true;
            $post->save();
            echo $e->getMessage();
        }
    }
}
