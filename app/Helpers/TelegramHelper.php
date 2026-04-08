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
        $this->bot->setCurlOption(CURLOPT_TIMEOUT, 0);
        $this->bot->setCurlOption(CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $this->bot->setCurlOption(CURLOPT_RETURNTRANSFER, true);
        $this->bot->setCurlOption(CURLOPT_NOPROGRESS, false);
        // Настройка CURL для использования SOCKS5 с авторизацией
        $this->bot->setCurlOption(CURLOPT_PROXY, '127.0.0.1:27504');
        $this->bot->setCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
        $this->bot->setCurlOption(CURLOPT_PROXYUSERPWD, 'test:83448344f');

        $this->bot->setCurlOption(
            CURLOPT_XFERINFOFUNCTION,
            function (
                $resource,
                $download_size,
                $downloaded,
                $upload_size,
                $uploaded
            ) {
                static $startTime = null;
                if ($upload_size > 0) {
                    if ($startTime === null) {
                        $startTime = microtime(true);
                    }

                    $elapsed = microtime(true) - $startTime;

                    $percent = round(($uploaded / $upload_size) * 100, 2);

                    $uploadedMB  = round($uploaded / 1024 / 1024, 2);
                    $totalMB     = round($upload_size / 1024 / 1024, 2);
                    $remainingMB = round(($upload_size - $uploaded) / 1024 / 1024, 2);

                    $speed = $elapsed > 0 ? $uploaded / $elapsed : 0; // bytes/sec
                    $speedMB = round($speed / 1024 / 1024, 2);

                    $eta = $speed > 0 ? round(($upload_size - $uploaded) / $speed) : 0;

                    $etaFormatted = gmdate("i:s", $eta);

                    echo sprintf(
                        "\r🚀 %6.2f%% | %6.2f / %6.2f MB | ⏳ ETA: %s | ⚡ %4.2f MB/s      ",
                        $percent,
                        $uploadedMB,
                        $totalMB,
                        $etaFormatted,
                        $speedMB
                    );
                }
            }
        );
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
        $data = @file_put_contents($tmpFile, file_get_contents($url));
        echo $data;
        if ($data === false) {
            // можно записать в лог
            //\Log::warning("Image not found: " . $url);

            return;
        }
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
                $data = @file_put_contents($tmpFile, file_get_contents($url));
                if ($data === false) {
                    // можно записать в лог
                    //\Log::warning("Image not found: " . $url);

                    continue;
                }

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
