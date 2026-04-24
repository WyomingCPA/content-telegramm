<?php

namespace App\Console\Commands\telegram;

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

use App\Helpers\TelegramHelper;

class QueueCatsVideoTelegrammCatsVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:queue-cats-video-telegramm-cats-video';

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
        //$isStart = Group::where('slug', '=', 'sexy')->first();
        //if (!$isStart->is_start) {
        //    echo "Не публикуем";
        //    return Command::SUCCESS;
        //}
        $telegram = new TelegramHelper();
        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $favorite_ids = $user->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)
            ->where('owner_id', 113)
            ->where('type', 'video')
            ->where('network', 'telegramm')
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
            $video = $post->attachments;
            $tags = '';
            foreach ($categories as $item_category) {
                $tags .= "#" . $item_category->name . " ";
            }
            $messageText .= "\n";
            $messageText .= $post->text;

            if (!empty($messageText)) {
                $chatId = '-1002315592624';
                $media = new ArrayOfInputMedia();
                $messageText .= "🐾 #cats \n\n\n<a href='https://t.me/+7yj6MB0l529lZmRi'>Cats ≽^•⩊•^≼</a>";

                //$media->addItem(new InputMediaVideo($video[1], $messageText, 'HTML'));

                //$bot->sendMediaGroup($chatId, $media);

                $telegram->sendVideos($chatId, $video[1], $messageText, 'HTML');
                $post->is_publish = true;
                $post->save();

                //$isStart->increment('posts_count');
            }
        } catch (\Error $e) {
            $user->queuesPost()->detach(array_values([$post->id]));
            $post->is_hidden = true;
            $post->save();

            echo $e->getMessage();
        }
    }
}
