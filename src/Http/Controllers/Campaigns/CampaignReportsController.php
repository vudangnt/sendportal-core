<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Models\Campaign;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Presenters\CampaignReportPresenter;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Sendportal\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Sendportal\Base\Repositories\TagTenantRepository;

class CampaignReportsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected $campaignRepo;

    /** @var MessageTenantRepositoryInterface */
    protected $messageRepo;

    /** @var TagTenantRepository */
    protected $tagRepo;

    public function __construct(
        CampaignTenantRepositoryInterface $campaignRepository,
        MessageTenantRepositoryInterface $messageRepo,
        TagTenantRepository $tagRepository
    ) {
        $this->campaignRepo = $campaignRepository;
        $this->messageRepo = $messageRepo;
        $this->tagRepo = $tagRepository;
    }

    /**
     * Show campaign report view.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(int $id, Request $request)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        $presenter = new CampaignReportPresenter($campaign, Sendportal::currentWorkspaceId(),
            (int)$request->get('interval', 24));
        $presenterData = $presenter->generate();

        $data = [
            'campaign' => $campaign,
            'campaignUrls' => $presenterData['campaignUrls'],
            'campaignStats' => $presenterData['campaignStats'],
            'chartLabels' => json_encode(Arr::get($presenterData['chartData'], 'labels', [])),
            'chartData' => json_encode(Arr::get($presenterData['chartData'], 'data', [])),
        ];

        return view('sendportal::campaigns.reports.index', $data);
    }

    /**
     * Show campaign recipients.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function recipients(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        $messages = $this->messageRepo->recipients(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return view('sendportal::campaigns.reports.recipients', compact('campaign', 'messages'));
    }

    /**
     * Export campaign recipients as CSV.
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws Exception
     */
    public function recipientsExport(int $id)
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $campaign = $this->campaignRepo->find($workspaceId, $id);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        $messages = $this->messageRepo->recipientsAll($workspaceId, Campaign::class, $id);

        if ($messages->isEmpty()) {
            return redirect()
                ->route('sendportal.campaigns.reports.recipients', $campaign->id)
                ->with('flash_notification', collect([[
                    'message' => __('Không có dữ liệu để xuất.'),
                    'level' => 'warning',
                ]]));
        }

        $filename = sprintf('campaign-%d-recipients-%s.csv', $campaign->id, date('Y-m-d-H-i-s'));

        return (new FastExcel($messages))->download($filename, static function (Message $message) {
            return [
                'message_id' => $message->id,
                'subscriber_id' => $message->subscriber_id,
                'subscriber_email' => $message->recipient_email,
                'subject' => $message->subject,
                'sent_at' => optional($message->sent_at)->toDateTimeString(),
                'delivered_at' => optional($message->delivered_at)->toDateTimeString(),
                'opened_at' => optional($message->opened_at)->toDateTimeString(),
                'open_count' => $message->open_count,
                'clicked_at' => optional($message->clicked_at)->toDateTimeString(),
                'click_count' => $message->click_count,
                'bounced_at' => optional($message->bounced_at)->toDateTimeString(),
                'complained_at' => optional($message->complained_at)->toDateTimeString(),
                'unsubscribed_at' => optional($message->unsubscribed_at)->toDateTimeString(),
            ];
        });
    }

    /**
     * Show campaign opens.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function opens(int $id, Request $request)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $averageTimeToOpen = $this->campaignRepo->getAverageTimeToOpen($campaign);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        [$orderBy, $direction] = $this->resolveOrdering($request, 'opened_at', ['open_count', 'opened_at', 'recipient_email', 'subject']);

        $messages = $this->messageRepo->opens(
            Sendportal::currentWorkspaceId(),
            Campaign::class,
            $id,
            [
                'order_by' => $orderBy,
                'direction' => $direction,
            ]
        );

        return view('sendportal::campaigns.reports.opens', compact('campaign', 'messages', 'averageTimeToOpen', 'orderBy', 'direction'));
    }

    /**
     * Show campaign clicks.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function clicks(int $id, Request $request)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $averageTimeToClick = $this->campaignRepo->getAverageTimeToClick($campaign);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        [$orderBy, $direction] = $this->resolveOrdering($request, 'clicked_at', ['click_count', 'clicked_at', 'recipient_email', 'subject']);

        $messages = $this->messageRepo->clicks(
            Sendportal::currentWorkspaceId(),
            Campaign::class,
            $id,
            [
                'order_by' => $orderBy,
                'direction' => $direction,
            ]
        );

        return view('sendportal::campaigns.reports.clicks', compact('campaign', 'messages', 'averageTimeToClick', 'orderBy', 'direction'));
    }

    /**
     * Show campaign bounces.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function bounces(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        $messages = $this->messageRepo->bounces(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return view('sendportal::campaigns.reports.bounces', compact('campaign', 'messages'));
    }

    /**
     * Show campaign unsubscribes.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function unsubscribes(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('sendportal.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        $messages = $this->messageRepo->unsubscribes(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return view('sendportal::campaigns.reports.unsubscribes', compact('campaign', 'messages'));
    }

    public function template(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $template = $campaign->template;

        return view('sendportal::campaigns.reports.templates', compact('campaign', 'template'));
    }

    public function bulkTag(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'message_ids' => ['required', 'array', 'min:1'],
            'message_ids.*' => ['integer'],
            'tag_label' => ['required', 'string', 'max:191'],
        ]);

        $workspaceId = Sendportal::currentWorkspaceId();
        $campaign = $this->campaignRepo->find($workspaceId, $id);

        $messages = Message::query()
            ->where('workspace_id', $workspaceId)
            ->where('source_type', Campaign::class)
            ->where('source_id', $campaign->id)
            ->whereIn('id', $request->input('message_ids', []))
            ->get();

        if ($messages->isEmpty()) {
            return $this->redirectWithFeedback($request, $campaign, __('Không tìm thấy bản ghi hợp lệ để gắn tag.'), 'danger');
        }

        $subscriberIds = $messages->pluck('subscriber_id')->filter()->unique()->values();

        if ($subscriberIds->isEmpty()) {
            return $this->redirectWithFeedback($request, $campaign, __('Không có subscriber tương ứng với các bản ghi đã chọn.'), 'warning');
        }

        $tagSuffix = trim($request->input('tag_label'));
        $tagName = 're_mkt_' . Str::slug($tagSuffix, '_');

        $tag = $this->tagRepo->findBy($workspaceId, 'name', $tagName);

        if (!$tag) {
            $tag = $this->tagRepo->store($workspaceId, [
                'name' => $tagName,
            ]);
        }

        $tag->subscribers()->syncWithoutDetaching($subscriberIds->all());

        $message = __('Đã gắn tag ":tag" cho :count subscriber.', [
            'tag' => $tag->name,
            'count' => $subscriberIds->count(),
        ]);

        return $this->redirectWithFeedback($request, $campaign, $message);
    }

    /**
     * @param array<string> $allowed
     *
     * @return array{0:string,1:string}
     */
    protected function resolveOrdering(Request $request, string $defaultColumn, array $allowed): array
    {
        $orderBy = $request->get('sort', $defaultColumn);
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (!in_array($orderBy, $allowed, true)) {
            $orderBy = $defaultColumn;
        }

        return [$orderBy, $direction];
    }

    protected function redirectWithFeedback(Request $request, Campaign $campaign, string $message, string $level = 'success'): RedirectResponse
    {
        $redirectUrl = $request->input('redirect_to');

        $targetRoute = $request->input('context') === 'clicks'
            ? route('sendportal.campaigns.reports.clicks', $campaign->id)
            : route('sendportal.campaigns.reports.opens', $campaign->id);

        $redirect = $redirectUrl ? redirect()->to($redirectUrl) : redirect()->to($targetRoute);

        return $redirect->with('flash_notification', collect([[
            'message' => $message,
            'level' => $level,
        ]]));
    }
}
