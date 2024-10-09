<?php

namespace Sendportal\Base\Http\Controllers;

use Carbon\CarbonPeriod;
use Exception;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Sendportal\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Sendportal\Base\Services\Campaigns\CampaignStatisticsService;

class DashboardController extends Controller
{
    /**
     * @var SubscriberTenantRepositoryInterface
     */
    protected $subscribers;

    /**
     * @var CampaignTenantRepositoryInterface
     */
    protected $campaigns;

    /**
     * @var MessageTenantRepositoryInterface
     */
    protected $messages;

    /**
     * @var CampaignStatisticsService
     */
    protected $campaignStatisticsService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        CampaignTenantRepositoryInterface $campaigns,
        MessageTenantRepositoryInterface $messages,
        CampaignStatisticsService $campaignStatisticsService
    ) {
        $this->subscribers = $subscribers;
        $this->campaigns = $campaigns;
        $this->messages = $messages;
        $this->campaignStatisticsService = $campaignStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $completedCampaigns = $this->campaigns->completedCampaigns($workspaceId, ['status']);
        $subscriberGrowthChart = $this->getSubscriberGrowthChart($workspaceId);
        $countActive = $this->subscribers->countActive($workspaceId);
        return view('sendportal::dashboard.index', [
            'recentSubscribers' => $this->subscribers->getRecentSubscribers($workspaceId),
            'completedCampaigns' => $completedCampaigns,
            'countActive' => $countActive,
            'campaignStats' => $this->campaignStatisticsService->getForCollection($completedCampaigns, $workspaceId),
            'subscriberGrowthChartLabels' => json_encode($subscriberGrowthChart['labels']),
            'subscriberGrowthChartData' => json_encode($subscriberGrowthChart['data']),
        ]);
    }

//    private function getCampaignStats(): array
//    {
//        $countData = $this->campaigns->getCounts(collect($this->campaign->id), $workspaceId);
//
//        return [
//            'counts' => [
//                'open' => (int) $countData[$this->campaign->id]->opened,
//                'click' => (int) $countData[$this->campaign->id]->clicked,
//                'sent' => $this->campaign->formatCount((int) $countData[$this->campaign->id]->sent),
//                'bounce' => (int) $countData[$this->campaign->id]->bounced,
//            ],
//            'ratios' => [
//                'open' => $this->campaign->getActionRatio((int) $countData[$this->campaign->id]->opened, (int) $countData[$this->campaign->id]->sent),
//                'click' => $this->campaign->getActionRatio((int) $countData[$this->campaign->id]->clicked, (int) $countData[$this->campaign->id]->sent),
//                'bounce' => $this->campaign->getActionRatio((int) $countData[$this->campaign->id]->bounced, (int) $countData[$this->campaign->id]->sent),
//            ],
//        ];
//    }

    protected function getSubscriberGrowthChart($workspaceId): array
    {
        $period = CarbonPeriod::create(now()->subDays(30)->startOfDay(), now()->endOfDay());

        $growthChartData = $this->subscribers->getGrowthChartData($period, $workspaceId);

        $growthChart = [
            'labels' => [],
            'data' => [],
        ];

        $currentTotal = $growthChartData['startingValue'];

        foreach ($period as $date) {
            $formattedDate = $date->format('d-m-Y');

            $periodValue = $growthChartData['runningTotal'][$formattedDate]->total ?? 0;
            $periodUnsubscribe = $growthChartData['unsubscribers'][$formattedDate]->total ?? 0;
            $currentTotal += $periodValue - $periodUnsubscribe;

            $growthChart['labels'][] = $formattedDate;
            $growthChart['data'][] = $currentTotal;
        }

        return $growthChart;
    }
}
