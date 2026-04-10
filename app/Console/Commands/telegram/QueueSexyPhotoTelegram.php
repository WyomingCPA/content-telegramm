<?php

namespace App\Console\Commands\telegram;

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
use App\Helpers\TelegramHelper;

class QueueSexyPhotoTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:queue-sexy-photo-telegram';

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
        $result = false;
        $telegram = new TelegramHelper();
        //Сделать проверку запуска публикаций для телеграмм
        $isStart = Group::where('slug', '=', 'sexy')->first();
        if (!$isStart->is_start) {
            echo "Не публикуем";
            return Command::SUCCESS;
        }

        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $favorite_ids = $user->queuesPost->pluck('id')->toArray();

        $objects = Post::where('is_publish', false)
            ->where('owner_id', 213)
            ->where('type', 'photo')
            ->where('network', 'telegramm')
            ->where('is_hidden', false)
            ->whereIn('id', $favorite_ids)
            ->orderBy('created_at', 'asc');

        $post = $objects->inRandomOrder()->first();
        echo $post->id;
        $post->touch();

        try {
            //Telegramm
            $messageText = '';
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
                $messageText = "#girl #body #fit \n\n\n<a href='https://t.me/+U0H_PQ6A29g0ZmVi'>Bikini Paradise</a>";
                //$bot->sendMessage($chatId, $messageText, 'HTML');
                $bot->setCurlOption(CURLOPT_TIMEOUT, 0);
                $media = new ArrayOfInputMedia();
                if (is_array($list_img[1])) {
                    $imgUrls = [];
                    foreach ($list_img[1] as $item_image) {
                        //$media->addItem(new InputMediaPhoto($item_image, $messageText, 'HTML'));
                        $imgUrls[] = $item_image;
                    }
                    // Отправка нескольких картинок
                    $result = $telegram->sendPhotos($chatId, $imgUrls, $messageText, 'HTML');
                    $post->is_publish = true;
                    $post->save();
                } else {
                    //Скачиваем картинку                  
                    //$media->addItem(new InputMediaPhoto($list_img[1], $messageText, 'HTML'));
                    $result = $telegram->sendPhotos($chatId, $list_img[1], $messageText, 'HTML');
                    $post->is_publish = true;
                    $post->save();
                }
                if ($result) {
                    $bot->sendMediaGroup($chatId, $media);
                    $post->is_publish = true;
                    $post->save();
                    $isStart->increment('posts_count');
                    echo 'Публикация выполена успешно';
                }
                else {
                    echo 'Публикация не выполнена';
                }
            }

        } catch (\Error $e) {
            $user->queuesPost()->detach(array_values([$post->id]));
            $post->is_hidden = true;
            $post->save();
            echo $e->getMessage();
        }
    }
}
