<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class StorageLinker
{
    public static function ensure(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        File::ensureDirectoryExists($target);

        if (is_link($link) && is_dir($link)) {
            return;
        }

        if (is_link($link) || (file_exists($link) && ! is_dir($link))) {
            @unlink($link);
        }

        if (is_dir($link) && ! is_link($link)) {
            return;
        }

        try {
            Artisan::call('storage:link', ['--force' => true]);
        } catch (\Throwable) {
            if (PHP_OS_FAMILY !== 'Windows') {
                @symlink($target, $link);
            }
        }
    }
}
