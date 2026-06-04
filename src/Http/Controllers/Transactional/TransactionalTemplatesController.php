<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Transactional;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Jobs\SendTransactionalMessageJob;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\Template;
use Sendportal\Base\Models\TransactionalSource;
use Sendportal\Base\Services\Templates\TemplateRenderer;
use Sendportal\Base\Services\Transactional\TransactionalEmailServiceResolver;

class TransactionalTemplatesController extends Controller
{
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $templates = Template::transactional()
            ->where('workspace_id', $workspaceId)
            ->orderBy('code')
            ->get();

        $defaults = Template::transactional()
            ->whereNull('workspace_id')
            ->where('is_default', true)
            ->get()
            ->keyBy('code');

        $templates = $templates->map(function ($t) use ($defaults) {
            $d = $defaults->get($t->code);
            if (!$d) {
                $t->source = 'custom';
            } else {
                $sigT = md5(($t->subject ?? '') . '|' . ($t->content ?? ''));
                $sigD = md5(($d->subject ?? '') . '|' . ($d->content ?? ''));
                $t->source = ($sigT === $sigD) ? 'seeded' : 'modified';
            }
            return $t;
        });

        return view('sendportal::templates.transactional.index', compact('templates'));
    }

    public function browseDefaults(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $defaults = Template::transactional()
            ->whereNull('workspace_id')
            ->where('is_default', true)
            ->orderBy('code')
            ->get();

        $cloned = Template::transactional()
            ->where('workspace_id', $workspaceId)
            ->pluck('id', 'code');

        return view('sendportal::templates.transactional.defaults', compact('defaults', 'cloned'));
    }

    public function clone(string $code): RedirectResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $default = Template::transactional()
            ->whereNull('workspace_id')
            ->where('code', $code)
            ->firstOrFail();

        $copy = Template::firstOrCreate(
            ['workspace_id' => $workspaceId, 'code' => $code],
            [
                'kind'       => Template::KIND_TRANSACTIONAL,
                'name'       => $default->name,
                'subject'    => $default->subject,
                'content'    => $default->content,
                'is_default' => false,
            ]
        );

        return redirect()
            ->route('sendportal.templates.transactional.edit', $copy->id)
            ->with('success', 'Cloned default into workspace.');
    }

    public function create(): View
    {
        return view('sendportal::templates.transactional.form', [
            'template' => null,
            'action'   => route('sendportal.templates.transactional.store'),
            'method'   => 'POST',
        ]);
    }

    public function edit(int $id): View
    {
        $template = Template::transactional()
            ->where('workspace_id', Sendportal::currentWorkspaceId())
            ->findOrFail($id);

        return view('sendportal::templates.transactional.form', [
            'template' => $template,
            'action'   => route('sendportal.templates.transactional.update', $id),
            'method'   => 'PUT',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $data = $this->validatePayload($request, $workspaceId);
        Template::create(array_merge($data, [
            'workspace_id' => $workspaceId,
            'kind'         => Template::KIND_TRANSACTIONAL,
            'is_default'   => false,
        ]));
        return redirect(url('/templates#transactional'))
            ->with('success', 'Template created');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $template = Template::transactional()->where('workspace_id', $workspaceId)->findOrFail($id);
        $template->update($this->validatePayload($request, $workspaceId, $id));
        return redirect(url('/templates#transactional'))
            ->with('success', 'Template updated');
    }

    public function destroy(int $id): RedirectResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $template = Template::transactional()->where('workspace_id', $workspaceId)->findOrFail($id);
        $template->delete();
        return redirect(url('/templates#transactional'))
            ->with('success', 'Template deleted');
    }

    public function test(Request $request, int $id): JsonResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $template = Template::transactional()
            ->where('workspace_id', $workspaceId)
            ->findOrFail($id);

        $data = $request->validate([
            'to_email'       => 'required|email',
            'from_email'     => 'required|email',
            'variables'      => 'nullable|array',
            'tracking.open'  => 'nullable|boolean',
            'tracking.click' => 'nullable|boolean',
        ]);

        $rendered = app(TemplateRenderer::class)->render($template, $data['variables'] ?? []);

        $emailService = app(TransactionalEmailServiceResolver::class)
            ->resolveStrict($workspaceId, $data['from_email']);
        if (!$emailService) {
            return response()->json([
                'error' => 'Sender domain not allowed for this workspace',
                'from_email' => $data['from_email'],
            ], 422);
        }

        $source = TransactionalSource::create([
            'workspace_id'    => $workspaceId,
            'request_payload' => [
                'metadata' => ['test' => true, 'template_id' => $template->id],
            ],
        ]);

        $message = Message::create([
            'workspace_id'    => $workspaceId,
            'subscriber_id'   => null,
            'source_type'     => TransactionalSource::class,
            'source_id'       => $source->id,
            'recipient_email' => $data['to_email'],
            'subject'         => $rendered['subject'],
            'from_email'      => $data['from_email'],
            'queued_at'       => now(),
        ]);

        SendTransactionalMessageJob::dispatch($message->id, $emailService->id);

        return response()->json([
            'test_message_hash'  => $message->hash,
            'transactional_hash' => $source->hash,
            'tracking_url'       => "/transactional/{$source->hash}",
            'rendered' => [
                'subject'           => $rendered['subject'],
                'preview_first_200' => mb_substr(strip_tags($rendered['content']), 0, 200),
            ],
        ], 202);
    }

    private function validatePayload(Request $request, int $workspaceId, ?int $ignoreId = null): array
    {
        $unique = sprintf(
            'unique:sendportal_templates,code,%s,id,workspace_id,%d',
            $ignoreId ?? 'NULL',
            $workspaceId
        );
        return $request->validate([
            'code'      => ['required', 'string', 'max:64', 'regex:/^[a-z0-9 _-]+$/', $unique],
            'name'      => 'required|string|max:255',
            'subject'   => 'nullable|string|max:998',
            'content'   => 'nullable|string',
            'data_json' => 'nullable|string',
        ]);
    }
}
