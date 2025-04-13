<?php

namespace Sendportal\Base\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sendportal\Base\Services\Subscribers\ImportSubscriberService;
use Illuminate\Support\Facades\Log;
use Sendportal\Base\Jobs\UpdateImportProgressJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;

class ImportSubscribersJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscribers;
    protected $workspaceId;
    protected $tags;
    protected $locations;
    protected $currentChunk;
    protected $totalChunks;

    public function __construct(
        array $subscribers, 
        int $workspaceId, 
        array $tags, 
        array $locations,
        int $currentChunk,
        int $totalChunks
    ) {
        $this->subscribers = $subscribers;
        $this->workspaceId = $workspaceId;
        $this->tags = $tags;
        $this->locations = $locations;
        $this->currentChunk = $currentChunk;
        $this->totalChunks = $totalChunks;
    }

    public function handle(ImportSubscriberService $importService)
    {
        foreach ($this->subscribers as $row) {
            try {
                $data = [
                    'id' => $row['id'] ?? null,
                    'email' => $row['email'],
                    'first_name' => $row['first_name'] ?? null,
                    'last_name' => $row['last_name'] ?? null,
                    'tags' => $this->tags,
                    'locations' => $this->locations
                ];

                $importService->import($this->workspaceId, $data);
            } catch (\Exception $e) {
                Log::error('Failed to import subscriber: ' . $e->getMessage(), [
                    'email' => $row['email'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // Cập nhật tiến trình sau khi xử lý xong chunk
        TrackImportProgressJob::dispatch(
            $this->workspaceId,
            $this->totalChunks,
            $this->currentChunk
        )->onQueue('default');
    }

    public function failed(\Throwable $exception)
    {
        // Xử lý khi job thất bại
        Log::error('Import subscribers failed: ' . $exception->getMessage());
    }
}
