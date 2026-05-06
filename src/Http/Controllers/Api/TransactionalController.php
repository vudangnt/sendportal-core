<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\SendTransactionalEmailRequest;
use Sendportal\Base\Jobs\SendTransactionalMessageJob;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\TransactionalSource;
use Sendportal\Base\Services\Transactional\TransactionalEmailServiceResolver;

class TransactionalController extends Controller
{
    protected TransactionalEmailServiceResolver $resolver;

    public function __construct(TransactionalEmailServiceResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Send a transactional email.
     */
    public function send(SendTransactionalEmailRequest $request): JsonResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $validated = $request->validated();

        $fromEmail = $validated['from']['email'];
        $emailService = $this->resolver->resolve($workspaceId, $fromEmail);

        if (!$emailService) {
            return response()->json([
                'error' => 'No email service configured for sender domain',
                'from_email' => $fromEmail,
            ], 422);
        }

        try {
            DB::beginTransaction();

            $source = TransactionalSource::create([
                'workspace_id' => $workspaceId,
                'request_payload' => $validated,
            ]);

            $messages = [];

            foreach ($validated['to'] as $recipient) {
                $message = Message::create([
                    'workspace_id' => $workspaceId,
                    'subscriber_id' => null,
                    'source_type' => TransactionalSource::class,
                    'source_id' => $source->id,
                    'recipient_email' => $recipient['email'],
                    'subject' => $validated['subject'],
                    'from_name' => $validated['from']['name'] ?? null,
                    'from_email' => $fromEmail,
                    'queued_at' => now(),
                ]);

                SendTransactionalMessageJob::dispatch($message->id, $emailService->id);

                $messages[] = [
                    'message_hash' => $message->hash,
                    'recipient' => $recipient['email'],
                ];
            }

            DB::commit();

            return response()->json([
                'transactional_hash' => $source->hash,
                'messages' => $messages,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to queue transactional email',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
