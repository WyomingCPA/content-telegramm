<?php

namespace App\Console\Commands\Ads;

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

class CreateSexy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sexy-ads';

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
        //Добавить проверку на запуск
        //Сделать проверку запуска публикаций для телеграмм
        $isStart = Group::where('slug', '=', 'anime')->first();
        if (!$isStart->is_start) {
            echo "Не публикуем";
            return Command::SUCCESS;
        }
        
        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $favorite_ids = $user->queuesPost->pluck('id')->toArray();
        $objects = Post::where('is_publish', false)->where('is_hidden', false)->whereIn('id', $favorite_ids)->orderBy('created_at', 'desc');

        $category_value = ['sexy'];
        $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();
        $objects->whereHas('categories', function ($query) use ($category_ids) {
            $query->whereIn('category_id', array_values($category_ids));
        });

        if ($objects->count() == 0) {
            $objects =  Post::where('is_publish', true)->where('is_hidden', false)->orderBy('updated_at', 'asc');
            $category_value = ['sexy'];
            $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();
            $objects->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('category_id', array_values($category_ids));
            });
            //echo $objects->count();
        }

        $post = $objects->inRandomOrder()->first();

        $bot = new BotApi(env('TELEGRAM_TOKEN'));
        // ID группы или канала, куда отправляем
        $chatId = -1001771871700;
        $ttlHours = 3; //Время жизни сообщения в часах
        $text = "Check Other Channels 📌";

        // Создаём inline-клавиатуру

        $keyboard = new InlineKeyboardMarkup([
            [['text' => '❤ Sexy View ❤', 'url' => 'https://t.me/+bnRfpjt2rJdmYzgy']],
        ]);

        // Отправляем сообщение с кнопками
        $media = new ArrayOfInputMedia();
        //$media->addItem(new InputMediaVideo($video[1][0], $messageText, 'HTML'));
        //$bot->sendMediaGroup($chatId, $media);
        $list_img = $post->attachments;

        $message = $bot->sendPhoto($chatId, $list_img[0][0], null, false, $keyboard);
        $messageId = $message->getMessageId();

        AdsMessage::create([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'delete_after' => Carbon::now()->addHours($ttlHours),
        ]);
    }
}
