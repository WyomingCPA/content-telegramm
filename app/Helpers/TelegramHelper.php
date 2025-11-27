<?php

namespace App\Helpers;

use TelegramBot\Api\BotApi;
use CURLFile;

class TelegramHelper
{
    protected BotApi $bot;

    public function __construct()
    {
        $botToken = env('TELEGRAM_TOKEN'); // храните токен в config/services.php
        $this->bot = new BotApi($botToken);
    }

    /**
     * Отправка одного или нескольких изображений в Telegram.
     *
     * @param string|array $chatId
     * @param string|array $imageUrls
     * @return void
     */
    public function sendPhotos(string|array $chatId, string|array $imageUrls, ?string $caption = null, string $parseMode = 'Markdown'): void
    {
        $imageUrls = (array) $imageUrls;

        if (count($imageUrls) === 1) {
            $this->sendSinglePhoto($chatId, $imageUrls[0], $caption, $parseMode);
        } else {
            $this->sendMultiplePhotos($chatId, $imageUrls, $caption, $parseMode);
        }
    }

    public function sendVideos(string|array $chatId, string|array $urls, ?string $caption = null, string $parseMode = 'Markdown'): void
{
    if (is_string($urls)) {
        $this->sendSingleVideo($chatId, $urls, $caption, $parseMode);
    } elseif (is_array($urls)) {
        //$this->sendMultipleVideos($chatId, $urls, $caption, $parseMode);
    } else {
        throw new \InvalidArgumentException("urls должен быть строкой или массивом");
    }
}

    protected function sendSinglePhoto(string|array $chatId, string $url, ?string $caption, string $parseMode): void
    {
        $tmpFile = sys_get_temp_dir() . "/tg_" . uniqid() . ".jpg";
        file_put_contents($tmpFile, file_get_contents($url));

        $this->bot->sendPhoto($chatId, new CURLFile($tmpFile), $caption ?? '', null, null, false, $parseMode);
        unlink($tmpFile);
    }

    protected function sendMultiplePhotos(string|array $chatId, array $urls, ?string $caption, string $parseMode): void
    {
        $chunks = array_chunk($urls, 10); // Telegram поддерживает до 10 фото в sendMediaGroup

        foreach ($chunks as $chunk) {
            $media = [];
            $files = [];
            $first = true;

            foreach ($chunk as $url) {
                $tmpFile = sys_get_temp_dir() . "/tg_" . uniqid() . ".jpg";
                file_put_contents($tmpFile, file_get_contents($url));

                $item = [
                    'type'  => 'photo',
                    'media' => 'attach://' . basename($tmpFile),
                ];

                // caption можно только к одной фотке (обычно первой)
                if ($first && $caption) {
                    $item['caption']    = $caption;
                    $item['parse_mode'] = $parseMode;
                    $first = false;
                }

                $media[] = $item;
                $files[basename($tmpFile)] = new CURLFile($tmpFile);
            }

            $postFields = array_merge([
                'chat_id' => $chatId,
                'media'   => json_encode($media, JSON_UNESCAPED_UNICODE)
            ], $files);

            $token = env('TELEGRAM_TOKEN');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$token}/sendMediaGroup");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            $response = curl_exec($ch);
            curl_close($ch);

            // Удаляем временные файлы
            foreach ($files as $file) {
                @unlink($file->getFilename());
            }
        }
    }
    protected function sendSingleVideo(string|array $chatId, string $url, ?string $caption = null, string $parseMode = 'Markdown'): void
    {
        $tmpFile = sys_get_temp_dir() . "/tg_" . uniqid() . ".mp4";
        file_put_contents($tmpFile, file_get_contents($url));

        $this->bot->sendVideo(
            $chatId,
            new CURLFile($tmpFile),
            null,       // duration
            null,       // width
            null,       // height
            null,       // thumb
            $caption ?? '',
            $parseMode
        );

        unlink($tmpFile);
    }
}
