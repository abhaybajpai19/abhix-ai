<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneInactiveUsers extends Command
{
    protected $signature = 'users:prune-inactive {--days=15 : Days without login before deletion}';

    protected $description = 'Delete accounts inactive for the given number of days, including chats and profile data';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $users = User::query()
            ->whereRaw('COALESCE(last_login_at, created_at) < ?', [$cutoff])
            ->get();

        if ($users->isEmpty()) {
            $this->info('No inactive users to prune.');

            return self::SUCCESS;
        }

        $deleted = 0;

        foreach ($users as $user) {
            $this->deleteUserData($user);
            $user->delete();
            $deleted++;
        }

        $this->info("Pruned {$deleted} inactive user(s) (no login for {$days}+ days).");

        return self::SUCCESS;
    }

    private function deleteUserData(User $user): void
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->chats()->each(function (Chat $chat) {
            foreach ($chat->messages as $message) {
                if (str_starts_with($message->message, '__IMAGE__:')) {
                    $path = str_replace('__IMAGE__:', '', $message->message);
                    $storagePath = str_replace('/storage/', '', parse_url($path, PHP_URL_PATH) ?? '');
                    if ($storagePath) {
                        Storage::disk('public')->delete($storagePath);
                    }
                }
            }
        });

        $user->chats()->delete();
    }
}
