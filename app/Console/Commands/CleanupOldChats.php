<?php

namespace App\Console\Commands;

use App\Models\ChatConversation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOldChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:cleanup {--days=30 : Number of days after which closed chats are deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete closed chat conversations older than specified days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->info("Cleaning up closed chat conversations older than {$days} days...");

        $cutoffDate = now()->subDays($days);

        // Count conversations to be deleted
        $count = ChatConversation::where('status', 'closed')
            ->where('updated_at', '<', $cutoffDate)
            ->count();

        if ($count === 0) {
            $this->info('No old conversations to clean up.');
            return self::SUCCESS;
        }

        // Delete old closed conversations (messages will cascade delete)
        $deleted = ChatConversation::where('status', 'closed')
            ->where('updated_at', '<', $cutoffDate)
            ->delete();

        $this->info("Successfully deleted {$deleted} old chat conversations.");
        Log::info("Chat cleanup: Deleted {$deleted} conversations older than {$days} days.");

        return self::SUCCESS;
    }
}
