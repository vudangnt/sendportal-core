<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\TransactionalSource;

class TransactionalMessagesController extends Controller
{
    /**
     * Show all transactional messages with filters.
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $query = Message::where('workspace_id', $workspaceId)
            ->where('source_type', TransactionalSource::class)
            ->with('source');

        // Filter by status
        if ($status = $request->input('status')) {
            $this->applyStatusFilter($query, $status);
        }

        // Filter by sender domain
        if ($senderDomain = $request->input('sender_domain')) {
            $query->where('from_email', 'like', '%@' . $senderDomain);
        }

        // Filter by recipient domain
        if ($recipientDomain = $request->input('recipient_domain')) {
            $query->where('recipient_email', 'like', '%@' . $recipientDomain);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('from_email', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($from = $request->input('from')) {
            $query->whereDate('queued_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('queued_at', '<=', $to);
        }

        $messages = $query->orderBy('queued_at', 'desc')->paginate(50);

        // Aggregate filter options
        $senderDomains = $this->extractDomains(
            Message::where('workspace_id', $workspaceId)
                ->where('source_type', TransactionalSource::class)
                ->distinct()
                ->pluck('from_email')
        );

        $recipientDomains = $this->extractDomains(
            Message::where('workspace_id', $workspaceId)
                ->where('source_type', TransactionalSource::class)
                ->distinct()
                ->pluck('recipient_email')
        );

        // Stats
        $stats = $this->getStats($workspaceId);

        return view('sendportal::transactional.index', compact(
            'messages',
            'senderDomains',
            'recipientDomains',
            'stats'
        ));
    }

    /**
     * Show a single transactional message.
     *
     * @throws Exception
     */
    public function show(int $id): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $message = Message::where('workspace_id', $workspaceId)
            ->where('source_type', TransactionalSource::class)
            ->where('id', $id)
            ->with('source')
            ->firstOrFail();

        return view('sendportal::transactional.show', compact('message'));
    }

    /**
     * Apply status filter to query.
     */
    protected function applyStatusFilter($query, string $status): void
    {
        switch ($status) {
            case 'queued':
                $query->whereNull('sent_at');
                break;
            case 'sent':
                $query->whereNotNull('sent_at')->whereNull('delivered_at');
                break;
            case 'delivered':
                $query->whereNotNull('delivered_at')
                    ->whereNull('opened_at')
                    ->whereNull('bounced_at')
                    ->whereNull('complained_at');
                break;
            case 'opened':
                $query->whereNotNull('opened_at')->whereNull('clicked_at');
                break;
            case 'clicked':
                $query->whereNotNull('clicked_at');
                break;
            case 'bounced':
                $query->whereNotNull('bounced_at');
                break;
            case 'complained':
                $query->whereNotNull('complained_at');
                break;
        }
    }

    /**
     * Extract unique domains from email collection.
     */
    protected function extractDomains($emails): array
    {
        $domains = [];
        foreach ($emails as $email) {
            if ($email && strpos($email, '@') !== false) {
                $domain = substr($email, strpos($email, '@') + 1);
                if (!in_array($domain, $domains, true)) {
                    $domains[] = $domain;
                }
            }
        }
        sort($domains);
        return $domains;
    }

    /**
     * Get aggregated stats for the workspace.
     */
    protected function getStats(int $workspaceId): array
    {
        $base = Message::where('workspace_id', $workspaceId)
            ->where('source_type', TransactionalSource::class);

        return [
            'total' => (clone $base)->count(),
            'sent' => (clone $base)->whereNotNull('sent_at')->count(),
            'delivered' => (clone $base)->whereNotNull('delivered_at')->count(),
            'opened' => (clone $base)->whereNotNull('opened_at')->count(),
            'clicked' => (clone $base)->whereNotNull('clicked_at')->count(),
            'bounced' => (clone $base)->whereNotNull('bounced_at')->count(),
            'complained' => (clone $base)->whereNotNull('complained_at')->count(),
        ];
    }
}
