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

    /**
     * Số lần retry tối đa (1 = không retry khi timeout)
     */
    public $tries = 1;

    /**
     * Timeout tối đa cho job (giây) - 30 phút
     */
    public $timeout = 1800;

    /**
     * Không tính retry khi bị timeout
     */
    public $maxExceptions = 1;

    protected $subscribers;
    protected $workspaceId;
    protected $tags;
    protected $locations;
    protected $skills;
    protected $industries;
    protected $levels;
    protected $currentChunk;
    protected $totalChunks;

    public function __construct(
        array $subscribers, 
        int $workspaceId, 
        array $tags, 
        array $locations,
        array $skills,
        array $industries,
        array $levels,
        int $currentChunk,
        int $totalChunks
    ) {
        $this->subscribers = $subscribers;
        $this->workspaceId = $workspaceId;
        $this->tags = $tags;
        $this->locations = $locations;
        $this->skills = $skills;
        $this->industries = $industries;
        $this->levels = $levels;
        $this->currentChunk = $currentChunk;
        $this->totalChunks = $totalChunks;
    }

    public function handle(ImportSubscriberService $importService)
    {
        $position = 0;
        $invalidRows = [];

        foreach ($this->subscribers as $row) {
            $position++;

            try {
                // Kiểm tra email có tồn tại và không rỗng
                if (empty($row['email'] ?? null)) {
                    Log::warning('Skipping subscriber with missing or empty email', [
                        'row' => $row
                    ]);
                    continue;
                }

                // SES tu choi dia chi co khoang trang, thieu @domain, domain ket thuc bang dau cham...
                $email = trim((string) $row['email']);

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidRows[] = $position;
                    continue;
                }

                $data = [
                    'id' => $row['id'] ?? null,
                    'email' => $email,
                    'first_name' => $row['first_name'] ?? null,
                    'last_name' => $row['last_name'] ?? null,
                    'tags' => $this->tags,
                    'locations' => $this->locations,
                    'skills' => $this->skills,
                    'industries' => $this->industries,
                    'levels' => $this->levels,
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

        // Một dòng log cho cả chunk: số dòng bị bỏ + vị trí dòng trong chunk, KHÔNG kèm email.
        if ($invalidRows !== []) {
            Log::warning('Skipping subscribers with invalid email format', [
                'workspace_id' => $this->workspaceId,
                'chunk' => $this->currentChunk,
                'total_chunks' => $this->totalChunks,
                'count' => count($invalidRows),
                'rows_in_chunk' => array_slice($invalidRows, 0, 50),
            ]);
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
