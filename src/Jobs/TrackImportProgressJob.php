<?php

namespace Sendportal\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class TrackImportProgressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $workspaceId;
    protected $totalChunks;
    protected $completedChunks;

    public function __construct(int $workspaceId, int $totalChunks, int $completedChunks = 0)
    {
        $this->workspaceId = $workspaceId;
        $this->totalChunks = $totalChunks;
        $this->completedChunks = $completedChunks;
    }

    public function handle()
    {
        $cacheKey = "import_progress_{$this->workspaceId}";
        $progress = ($this->completedChunks / $this->totalChunks) * 100;

        Cache::put($cacheKey, [
            'progress' => $progress,
            'completed_chunks' => $this->completedChunks,
            'total_chunks' => $this->totalChunks,
            'updated_at' => now(),
            'estimated_time' => $this->calculateEstimatedTime()
        ], now()->addHours(1));
    }

    protected function calculateEstimatedTime()
    {
        if ($this->completedChunks === 0) {
            return null;
        }

        $startTime = Cache::get("import_start_time_{$this->workspaceId}");
        if (!$startTime) {
            return null;
        }

        $elapsed = now()->diffInSeconds($startTime);
        
        // Tránh lỗi division by zero khi elapsed = 0
        if ($elapsed === 0) {
            return null;
        }

        $rate = $this->completedChunks / $elapsed;
        
        // Tránh lỗi division by zero khi rate = 0
        if ($rate === 0) {
            return null;
        }
        
        $remaining = ($this->totalChunks - $this->completedChunks) / $rate;

        return ceil($remaining);
    }
}
