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
class CreateAdvertListButton extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-advert-list-button';

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
        $bot = new BotApi(env('TELEGRAM_TOKEN'));
        // ID группы или канала, куда отправляем
        //$chatId = -414528593;
        $ttlHours = 7; //Время жизни сообщения в часах
        $text = "🔥🔥🔥🔥🔥🔥🔥";

        $chatIds = [
            //-414528593,
            -1002366645779,
            -1001771871700,
        ];
        // Создаём inline-клавиатуру
        $keyboard = new InlineKeyboardMarkup([
            [
                ['text' => '🗝️❤ Тайник Кати', 'url' => 'https://simpleitrunner.ru/c0xi'],
                ['text' => '🤖✨Flirr AI', 'url' => 'https://simpleitrunner.ru/ja4d'],
                ['text' => '💡📱 Simple GPT', 'url' => 'https://simpleitrunner.ru/mww0'],
            ],
            [
                ['text' => 'Премиум храм', 'url' => 'https://simpleitrunner.ru/b7vv'],
                ['text' => '🔒Prison', 'url' => 'https://simpleitrunner.ru/l69e'],
                ['text' => 'Blocus AI', 'url' => 'https://simpleitrunner.ru/t5ii'],
            ],
            [
                ['text' => '🔥Trusty Talk (18+)', 'url' => 'https://simpleitrunner.ru/83co'],
                ['text' => '🌸💭Waifu Dream', 'url' => 'https://simpleitrunner.ru/4wnz'],
                ['text' => '🌙✨Ira moon', 'url' => 'https://simpleitrunner.ru/2xpv'],
            ],
            [
                ['text' => '🔥💋Hot Chat AI (18+)', 'url' => 'https://simpleitrunner.ru/5pla'],
                ['text' => '💘Date Me', 'url' => 'https://simpleitrunner.ru/mcl6'],
                ['text' => '🎁✨Случайный подарок', 'url' => 'https://simpleitrunner.ru/oxox'],
            ],
        ]);
        foreach ($chatIds as $chatId) {
            $message = $bot->sendMessage(
                $chatId,
                $text,
                'HTML',
                false,
                null,
                $keyboard
            );

            $messageId = $message->getMessageId();

            AdsMessage::create([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'delete_after' => Carbon::now()->addHours($ttlHours),
            ]);
        }

    }
}
