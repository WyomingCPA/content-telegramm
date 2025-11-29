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

class CreateAdvertTixListButton extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-advert-tix-list-button';

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
        $text = "TIX farms while you chill 😎🔥";

        $chatIds = [
            //-414528593,
            -1002366645779,
            -1001771871700,
        ];
        // Создаём inline-клавиатуру
        $keyboard = new InlineKeyboardMarkup([
            [
                ['text' => '👉 Перейти в группу', 'url' => 'https://t.me/tix_pools'],
            ],
            [
                ['text' => 'Пул/обмен TIX', 'url' => 'https://app.ston.fi/pools/EQDGCxjp13M_62-3TQ0vtRhxLe-TTviGg7rtCWrnoje2THhh'],
            ],
            [
                ['text' => 'Стейкинг TIX', 'url' => 'https://jvault.xyz/staking/v2/stake/TIX-MARS'],
            ],
            [
                ['text' => '🧡BeeHarvest 🐝 Game', 'url' => 'https://t.me/beeharvestbot?start=1511802093'],
                ['text' => '🔴MARS Game', 'url' => 'https://tonpl.com/r1466'],
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
