<?php

namespace App\Console\Commands;

use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearExpiredSubscriptions extends Command
{
    protected $signature = 'subscribe:clear-expired';
    protected $description = 'Clear expired subscriptions from users and mark them as canceled';

    public function handle(): int
    {
        $now = now();

        $expiredSubscriptions = Subscribe::where('subscribe_end_at', '<', $now)
            ->whereNull('subscribe_canceled_at')
            ->get();

        $cleared = 0;

        foreach ($expiredSubscriptions as $sub) {
            $user = User::where('subscribe', $sub->subscribe_id)->first();
            if ($user) {
                $user->update([
                    'subscribe' => null,
                    'role' => 'user',
                ]);
                $sub->update([
                    'subscribe_canceled_at' => $now,
                    'subscribe_updated_at' => $now,
                ]);
                $cleared++;
                $this->line("Cleared: user_id={$user->id} subscribe_id={$sub->subscribe_id} end_at={$sub->subscribe_end_at}");
                Log::info("ClearExpiredSubscription: user_id={$user->id} subscribe_id={$sub->subscribe_id} end_at={$sub->subscribe_end_at}");
            }
        }

        $this->info("Done. Cleared={$cleared} total_expired={$expiredSubscriptions->count()}");
        Log::info("ClearExpiredSubscriptions: cleared={$cleared} total_expired={$expiredSubscriptions->count()}");

        return self::SUCCESS;
    }
}
