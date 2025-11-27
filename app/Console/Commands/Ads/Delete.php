<?php

namespace App\Console\Commands\Ads;

use Illuminate\Console\Command;

use App\Models\AdsMessage;
use Carbon\Carbon;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use Illuminate\Support\Facades\DB;

class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-ads';

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
        // Получаем список постов из таблицы 

        $ads = AdsMessage::where('delete_after', '<=', Carbon::now())->get();
        foreach ($ads as $ad) {
            try {
                $bot->deleteMessage($ad->chat_id, $ad->message_id);
                $this->info("Удалено сообщение ID: {$ad->message_id}");
                $ad->delete();
            } catch (\Exception $e) {
                $this->error("Ошибка при удалении {$ad->message_id}: " . $e->getMessage());
            }
        }
    }
}
