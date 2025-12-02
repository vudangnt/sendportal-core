<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignDeleteController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected $campaigns;

    public function __construct(CampaignTenantRepositoryInterface $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * Show a confirmation view prior to deletion.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function confirm(int $id)
    {
        $campaign = $this->campaigns->find(Sendportal::currentWorkspaceId(), $id);

        return view('sendportal::campaigns.delete', compact('campaign'));
    }

    /**
     * Delete a campaign from the database.
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'delete_type' => 'nullable|in:hide,force',
        ]);

        $campaign = $this->campaigns->find(Sendportal::currentWorkspaceId(), $id);
        $deleteType = $request->get('delete_type', 'hide'); // Default to 'hide' if not provided

        if ($deleteType === 'force') {
            // Force delete - xóa vĩnh viễn
            if (!$campaign->draft) {
                return redirect()->route('sendportal.campaigns.index')
                    ->withErrors(__('Chỉ có thể xóa vĩnh viễn campaign ở trạng thái draft.'));
            }
            
            // Force delete directly from model
            $campaign->forceDelete();
            $message = __('Campaign đã được xóa vĩnh viễn.');
        } else {
            // Soft delete - ẩn campaign (default)
            $this->campaigns->destroy(Sendportal::currentWorkspaceId(), $id);
            $message = __('Campaign đã được ẩn.');
        }

        return redirect()->route('sendportal.campaigns.index')
            ->with('success', $message);
    }
}
