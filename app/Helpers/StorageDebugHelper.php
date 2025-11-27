<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StorageDebugHelper
{
    /**
     * Диагностика сохранения файла в Storage.
     *
     * @param string $url      — URL файла
     * @param string $disk     — имя диска из config/filesystems.php
     * @param string $savePath — путь внутри диска
     * @return void
     */
    public static function saveFromUrlDebug(string $url, string $disk, string $savePath): void
    {
        echo "=== ДИАГНОСТИКА STORAGE ===\n";

        // 1. Проверяем диск
        try {
            $driver = Storage::disk($disk);
            echo "[OK] Диск '{$disk}' найден\n";
        } catch (\Throwable $e) {
            dd("[ERR] Диск '{$disk}' не найден", $e->getMessage());
        }

        // 2. Загружаем файл
        $response = Http::get($url);
        if (!$response->successful()) {
            dd("[ERR] Ошибка HTTP: {$response->status()}");
        }
        $body = $response->body();
        if (empty($body)) {
            dd("[ERR] Пустой ответ от URL");
        }
        echo "[OK] Файл загружен, размер: " . strlen($body) . " байт\n";

        // 3. Создаём директорию
        $dir = dirname($savePath);
        if (!Storage::disk($disk)->exists($dir)) {
            if (!Storage::disk($disk)->makeDirectory($dir)) {
                dd("[ERR] Не удалось создать директорию '{$dir}'");
            }
            echo "[OK] Директория '{$dir}' создана\n";
        } else {
            echo "[OK] Директория '{$dir}' уже есть\n";
        }

        // 4. Пишем файл
        $saved = Storage::disk($disk)->put($savePath, $body);
        if (!$saved) {
            dd("[ERR] Не удалось сохранить файл '{$savePath}'");
        }
        echo "[OK] Файл сохранён в '{$savePath}'\n";

        // 5. Полный путь (только для Local драйвера)
        try {
            $fullPath = Storage::disk($disk)->path($savePath);
            echo "[INFO] Полный путь: {$fullPath}\n";
        } catch (\Throwable $e) {
            echo "[INFO] Полный путь недоступен для этого драйвера\n";
        }

        echo "=== ДИАГНОСТИКА ЗАВЕРШЕНА ===\n";
    }
}