<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\SendTransactionalEmailRequest;
use Sendportal\Base\Http\Resources\TransactionalSourceResource;
use Sendportal\Base\Jobs\SendTransactionalMessageJob;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\TransactionalSource;
use Sendportal\Base\Services\Templates\TemplateRenderer;
use Sendportal\Base\Services\Templates\TransactionalTemplateResolver;
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

        // Resolve + render template if template_code provided
        $resolvedSubject = null;
        $resolvedContent = null;
        $templateCode    = null;

        if (!empty($validated['template_code'])) {
            $template = app(TransactionalTemplateResolver::class)
                ->resolveTemplate($workspaceId, $validated['template_code']);
            $rendered = app(TemplateRenderer::class)
                ->render($template, $validated['variables'] ?? []);

            $resolvedSubject = $rendered['subject'];
            $resolvedContent = $rendered['content'];
            $templateCode    = $template->code;
        }

        $subject     = $validated['subject']           ?? $resolvedSubject;
        $contentHtml = $validated['content']['html']   ?? $resolvedContent;

        if ($subject === null || $contentHtml === null) {
            return response()->json([
                'error' => 'Missing subject or content (provide them or supply a template_code).',
            ], 422);
        }

        $validated['subject'] = $subject;
        $validated['content'] = array_merge($validated['content'] ?? [], [
            'type' => $validated['content']['type'] ?? 'html',
            'html' => $contentHtml,
        ]);

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
                    'subject' => $subject,
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
                'template_code'      => $templateCode,
                'messages'           => $messages,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to queue transactional email',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List transactional sources for the current workspace.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $sources = TransactionalSource::where('workspace_id', $workspaceId)
            ->with('messages')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 25));

        return TransactionalSourceResource::collection($sources);
    }

    /**
     * Show a single transactional source by hash.
     */
    public function show(string $hash): JsonResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $source = TransactionalSource::where('workspace_id', $workspaceId)
            ->where('hash', $hash)
            ->with('messages')
            ->first();

        if (!$source) {
            return response()->json([
                'error' => 'Transactional source not found',
            ], 404);
        }

        return response()->json(new TransactionalSourceResource($source));
    }
}
