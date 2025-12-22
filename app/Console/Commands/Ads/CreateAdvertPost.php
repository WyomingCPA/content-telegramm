<?php

namespace App\Console\Commands\Ads;

use CURLFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Views;
use App\Models\AdsMessage;
use Carbon\Carbon;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;


class CreateAdvertPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-advert-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда генерирует пост из админки';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isStart = Group::where('slug', '=', 'anime')->first();
        if (!$isStart->is_start) {
            echo "Не публикуем";
            return Command::SUCCESS;
        }
        $isStartSexy = Group::where('slug', '=', 'sexy')->first();
        if (!$isStartSexy->is_start) {
            echo "Не публикуем";
            return Command::SUCCESS;
        }

        $objects = Post::where('network', 'telegramm')
            ->where('type', 'ad')
            ->where('owner_id', 300)
            ->where('is_publish', true)
            ->orderBy('updated_at', 'desc');

        $post = $objects->inRandomOrder()->first();

        $bot = new BotApi(env('TELEGRAM_TOKEN'));
        // ID группы или канала, куда отправляем
        $chatIds = [
            //-414528593,
            -1002366645779,
            -1001771871700,
        ];
        //$chatId = -1001771871700;
        $ttlHours = 1; //Время жизни сообщения в часах
        $text = $post->text;
        $keyboard = new InlineKeyboardMarkup([
            [['text' => $text, 'url' => $post->link]],
        ]);
        $list_img = $post->attachments;
        //echo $list_img["image"];
        foreach ($chatIds as $chatId) {
            $tmpFile = storage_path("app/public") . "/" . $list_img["image"];
            echo $tmpFile;

            $message = $bot->sendPhoto($chatId, new CURLFile($tmpFile), null, false, $keyboard);
            $messageId = $message->getMessageId();

            AdsMessage::create([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'delete_after' => Carbon::now()->addHours($ttlHours),
            ]);
        }
    }
}
