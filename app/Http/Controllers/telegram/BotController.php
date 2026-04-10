<?php

namespace App\Http\Controllers\telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use TelegramBot\Api\BotApi;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Models\AdsMessage;
use \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class BotController extends Controller
{
    public function getStatistic(Request $request)
    {
        $list_chat_id = [
            '-1002366645779' => 'sexy',
            '-1001597866737' => 'estetic_vibes',
            '-1001771871700' => 'anime',
            '-1002315592624' => 'Котики'
        ];

        $bot = new BotApi(env('TELEGRAM_TOKEN'));

        $stat = array();
        $messageText = "<b>Статистика каналов Telegramm</b>\n\n";
        foreach ($list_chat_id as $key => $value) {
            $count_channel = $bot->getChatMembersCount($key);
            $queues_count = Post::queueCount($value);
            $stat[] = array(
                'count_member' => $count_channel,
                'channel' => $value,
                'id_channel' => $key,
                'count_queues' => $queues_count,
            );
            $messageText .= "$value = $count_channel Очередь = $queues_count\n";
        }

        return response()->json([
            'message' => $messageText,
            'status' => true,
        ], 200);
    }

    public function createListButtonAdvert(Request $request)
    {
        $messageResponse = '-';
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

        return response()->json([
            'message' => $messageResponse,
            'status' => true,
        ], 200);
    }
    public function createAnimeAdvert(Request $request)
    {
        $proxy_env = env('SERVER_PROXY');
        $proxy_password_env = env('PROXY_PASSWORD');

        $messageResponse = '-';
        $isStart = Group::where('slug', '=', 'sexy')->first();
        if (!$isStart->is_start) {
            $messageResponse = "Не публикуем";

            return response()->json([
                'message' => $messageResponse,
                'status' => true,
            ], 200);
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

        $bot = new BotApi(env('TELEGRAM_TOKEN'));
        $bot->setCurlOption(CURLOPT_TIMEOUT, 0);
        // Настройка CURL для использования SOCKS5 с авторизацией
        $bot->setCurlOption(CURLOPT_PROXY, $proxy_env); //'127.0.0.1:27504'
        //$this->bot->setCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
        $bot->setCurlOption(CURLOPT_PROXYUSERPWD, $proxy_password_env);
        // ID группы или канала, куда отправляем
        $chatId = -1002366645779;
        $ttlHours = 3; //Время жизни сообщения в секундах
        $text = "Check Other Channels 📌";

        // Создаём inline-клавиатуру с 3 кнопками

        $keyboard = new InlineKeyboardMarkup([
            [['text' => '❤ Anime View ❤', 'url' => 'https://t.me/+pgwp3k1AXf5kOThi'],],
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

        return response()->json([
            'message' => $messageResponse,
            'status' => true,
        ], 200);
    }

    public function createSexyAdvert(Request $request)
    {
        $proxy_env = env('SERVER_PROXY');
        $proxy_password_env = env('PROXY_PASSWORD');
        
        $messageResponse = '-';
        $isStart = Group::where('slug', '=', 'anime')->first();
        if (!$isStart->is_start) {
            $messageResponse = "Не публикуем";

            return response()->json([
                'message' => $messageResponse,
                'status' => true,
            ], 200);
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

        $bot->setCurlOption(CURLOPT_TIMEOUT, 0);
        // Настройка CURL для использования SOCKS5 с авторизацией
        $bot->setCurlOption(CURLOPT_PROXY, $proxy_env); //'127.0.0.1:27504'
        //$this->bot->setCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
        $bot->setCurlOption(CURLOPT_PROXYUSERPWD, $proxy_password_env);

        // ID группы или канала, куда отправляем
        $chatId = -1001771871700;
        $ttlHours = 3; //Время жизни сообщения в секундах
        $text = "Check Other Channels 📌";

        // Создаём inline-клавиатуру с 3 кнопками

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

        return response()->json([
            'message' => $messageResponse,
            'status' => true,
        ], 200);
    }


    public function updateStatusGroup(Request $request)
    {
        $message = '';
        $id_group = $request->id;
        $model = Group::find($id_group);
        if (!isset($model->is_start) || $model->is_start == false) {
            $model->is_start = true;
            $message = 'Группа включена';
        } else {
            $model->is_start = false;
            $message = 'Группа выключена';
        }
        $model->save();

        return response()->json([
            'message' => $message,
            'status' => true,
        ], 200);
    }

    public function getStatusGroups(Request $request)
    {
        $models = Group::all();

        return response()->json([
            'groups' => $models->toArray(),
            'status' => true,
        ], 200);
    }

    //Удалить все рекламмные посты во всех группах без условий
    public function deleteAllAdvertPost(Request $request)
    {

        return response()->json([
            'status' => true,
        ], 200);
    }
}
