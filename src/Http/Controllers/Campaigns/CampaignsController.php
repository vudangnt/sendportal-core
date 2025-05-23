<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\CampaignStoreRequest;
use Sendportal\Base\Models\EmailService;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Sendportal\Base\Repositories\EmailServiceTenantRepository;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Sendportal\Base\Repositories\TagTenantRepository;
use Sendportal\Base\Repositories\TemplateTenantRepository;
use Sendportal\Base\Services\Campaigns\CampaignStatisticsService;

class CampaignsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected $campaigns;

    /** @var TemplateTenantRepository */
    protected $templates;

    /** @var TagTenantRepository */
    protected $tags;

    /** @var EmailServiceTenantRepository */
    protected $emailServices;

    /** @var SubscriberTenantRepositoryInterface */
    protected $subscribers;

    /**
     * @var CampaignStatisticsService
     */
    protected $campaignStatisticsService;
    private LocationTenantRepository $locations;

    public function __construct(
        CampaignTenantRepositoryInterface $campaigns,
        TemplateTenantRepository $templates,
        TagTenantRepository $tags,
        LocationTenantRepository $locations,
        EmailServiceTenantRepository $emailServices,
        SubscriberTenantRepositoryInterface $subscribers,
        CampaignStatisticsService $campaignStatisticsService
    ) {
        $this->campaigns = $campaigns;
        $this->templates = $templates;
        $this->tags = $tags;
        $this->locations = $locations;
        $this->emailServices = $emailServices;
        $this->subscribers = $subscribers;
        $this->campaignStatisticsService = $campaignStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): ViewContract
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $params = ['draft' => true];
        $campaigns = $this->campaigns->paginate($workspaceId, 'created_atDesc', ['status'], 25, $params);

        return view('sendportal::campaigns.index', [
            'campaigns' => $campaigns,
            'campaignStats' => $this->campaignStatisticsService->getForPaginator($campaigns, $workspaceId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function sent(): ViewContract
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $params = ['sent' => true];
        $campaigns = $this->campaigns->paginate($workspaceId, 'created_atDesc', ['status'], 25, $params);

        return view('sendportal::campaigns.index', [
            'campaigns' => $campaigns,
            'campaignStats' => $this->campaignStatisticsService->getForPaginator($campaigns, $workspaceId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function create(): ViewContract
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $templates = [null => '- None -'] + $this->templates->pluck($workspaceId);
        $emailServices = $this->emailServices->all(Sendportal::currentWorkspaceId(), 'id', ['type'])
            ->map(static function (EmailService $emailService) {
                $emailService->formatted_name = "{$emailService->name} ({$emailService->type->name})";
                $settings =  $emailService->settings;
                $emailService->domain =  $settings['domain'] ?? null;
                return $emailService;
            });

        return view('sendportal::campaigns.create', compact('templates', 'emailServices'));
    }

    /**
     * @throws Exception
     */
    public function store(CampaignStoreRequest $request): RedirectResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $campaign = $this->campaigns->store($workspaceId, $this->handleCheckboxes($request->validated()));

        return redirect()->route('sendportal.campaigns.preview', $campaign->id);
    }

    /**
     * Handle checkbox fields.
     *
     * NOTE(david): this is here because the Campaign model is marked as being unable to use boolean fields.
     */
    private function handleCheckboxes(array $input): array
    {
        $checkboxFields = [
            'is_open_tracking',
            'is_click_tracking'
        ];

        foreach ($checkboxFields as $checkboxField) {
            if (!isset($input[$checkboxField])) {
                $input[$checkboxField] = false;
            }
        }

        return $input;
    }

    /**
     * @throws Exception
     */
    public function show(int $id): ViewContract
    {
        $campaign = $this->campaigns->find(Sendportal::currentWorkspaceId(), $id);

        return view('sendportal::campaigns.show', compact('campaign'));
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): ViewContract
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $campaign = $this->campaigns->find($workspaceId, $id);
        $emailServices = $this->emailServices->all($workspaceId, 'id', ['type'])
            ->map(static function (EmailService $emailService) {
                $emailService->formatted_name = "{$emailService->name} ({$emailService->type->name})";
                $settings =  $emailService->settings;
                $emailService->domain =  $settings['domain'] ?? null;
                return $emailService;
            });
        $templates = [null => '- None -'] + $this->templates->pluck($workspaceId);

        $email = $campaign->from_email ?? old('from_email');
        $emailParts = explode('@', $email); // Tách email thành 2 phần
        $username = $emailParts[0] ?? ''; // Lấy phần username
        $domain = $emailParts[1] ?? ''; // Lấy phần domain

        $campaign->from_email = $username;
        $campaign->from_domain = $domain;

        return view('sendportal::campaigns.edit', compact('campaign', 'emailServices', 'templates'));
    }

    /**
     * @throws Exception
     */
    public function update(int $campaignId, CampaignStoreRequest $request): RedirectResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $campaign = $this->campaigns->update(
            $workspaceId,
            $campaignId,
            $this->handleCheckboxes($request->validated())
        );

        return redirect()->route('sendportal.campaigns.preview', $campaign->id);
    }

    /**
     * @return RedirectResponse|ViewContract
     * @throws Exception
     */
    public function preview(int $id)
    {
        $campaign = $this->campaigns->find(Sendportal::currentWorkspaceId(), $id);
        $subscriberCount = $this->subscribers->countActive(Sendportal::currentWorkspaceId());

        if (!$campaign->draft) {
            return redirect()->route('sendportal.campaigns.status', $id);
        }

        $tags = $this->tags->all(Sendportal::currentWorkspaceId(), 'name')->toArray();
        $locations = $this->locations->all(Sendportal::currentWorkspaceId(), 'name')->toArray();

        foreach ($tags as $key => $tag) {
            if ($tag['parent_id'] === 0) {
                foreach ($tags as $child) {
                    if ($child['parent_id'] === $tag['id']) {
                        $tags[$key]['child'][] = $child;
                    }
                }
                $tags[$key]['child_count'] = count($tags[$key]['child']??[]);
            }
        }
        // Hàm lọc
        $tags = array_filter($tags, function ($item) {
            return $item['parent_id'] === 0;
        });


        foreach ($locations as $key => $location) {
            if ($location['parent_id'] === 0) {
                foreach ($locations as $child) {
                    if ($child['parent_id'] === $location['id']) {
                        $locations[$key]['child'][] = $child;
                    }
                }
                $locations[$key]['child_count'] = count($locations[$key]['child']??[]);
            }
        }
        // Hàm lọc
        $locations = array_filter($locations, function ($item) {
            return $item['parent_id'] === 0;
        });
        return view('sendportal::campaigns.preview', compact('campaign', 'tags', 'locations', 'subscriberCount'));
    }

    /**
     * @return RedirectResponse|ViewContract
     * @throws Exception
     */
    public function status(int $id)
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $campaign = $this->campaigns->find($workspaceId, $id, ['status']);

        if ($campaign->sent) {
            return redirect()->route('sendportal.campaigns.reports.index', $id);
        }

        return view('sendportal::campaigns.status', [
            'campaign' => $campaign,
            'campaignStats' => $this->campaignStatisticsService->getForCampaign($campaign, $workspaceId),
        ]);
    }
}
