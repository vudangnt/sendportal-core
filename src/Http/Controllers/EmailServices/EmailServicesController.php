<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\EmailServices;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\EmailServiceRequest;
use Sendportal\Base\Repositories\EmailServiceTenantRepository;

class EmailServicesController extends Controller
{
    /** @var EmailServiceTenantRepository */
    private $emailServices;

    public function __construct(EmailServiceTenantRepository $emailServices)
    {
        $this->emailServices = $emailServices;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $emailServices = $this->emailServices->all(Sendportal::currentWorkspaceId());

        return view('sendportal::email_services.index', compact('emailServices'));
    }

    public function create()
    {
        if (!config('sendportal-host.email_services.editable', true)) {
            return redirect()->route('sendportal.email_services.index')
                ->withErrors(__('You do not have permission to create email services.'));
        }

        $emailServiceTypes = $this->emailServices->getEmailServiceTypes()->pluck('name', 'id');

        return view('sendportal::email_services.create', compact('emailServiceTypes'));
    }

    /**
     * @throws Exception
     */
    public function store(EmailServiceRequest $request): RedirectResponse
    {
        if (!config('sendportal-host.email_services.editable', true)) {
            return redirect()->route('sendportal.email_services.index')
                ->withErrors(__('You do not have permission to create email services.'));
        }

        $emailServiceType = $this->emailServices->findType($request->type_id);

        $settings = $request->get('settings', []);

        $workspaceId = Sendportal::currentWorkspaceId();
        $isDefault = (bool) $request->input('is_default', false);

        if ($isDefault) {
            \Sendportal\Base\Models\EmailService::where('workspace_id', $workspaceId)
                ->update(['is_default' => false]);
        }

        $this->emailServices->store($workspaceId, [
            'name'           => $request->name,
            'type_id'        => $emailServiceType->id,
            'settings'       => $settings,
            'sender_domains' => $this->parseSenderDomains($request->input('sender_domains')),
            'is_default'     => $isDefault,
        ]);

        return redirect()->route('sendportal.email_services.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $emailServiceId)
    {
        if (!config('sendportal-host.email_services.editable', true)) {
            return redirect()->route('sendportal.email_services.index')
                ->withErrors(__('You do not have permission to edit email services.'));
        }

        $emailServiceTypes = $this->emailServices->getEmailServiceTypes()->pluck('name', 'id');
        $emailService = $this->emailServices->find(Sendportal::currentWorkspaceId(), $emailServiceId);
        $emailServiceType = $this->emailServices->findType($emailService->type_id);

        return view('sendportal::email_services.edit', compact('emailServiceTypes', 'emailService', 'emailServiceType'));
    }

    /**
     * @throws Exception
     */
    public function update(EmailServiceRequest $request, int $emailServiceId): RedirectResponse
    {
        if (!config('sendportal-host.email_services.editable', true)) {
            return redirect()->route('sendportal.email_services.index')
                ->withErrors(__('You do not have permission to edit email services.'));
        }

        $workspaceId = Sendportal::currentWorkspaceId();
        $emailService = $this->emailServices->find($workspaceId, $emailServiceId, ['type']);

        $settings = $request->get('settings');
        $isDefault = (bool) $request->input('is_default', false);

        if ($isDefault) {
            \Sendportal\Base\Models\EmailService::where('workspace_id', $workspaceId)
                ->where('id', '!=', $emailServiceId)
                ->update(['is_default' => false]);
        }

        $emailService->name = $request->name;
        $emailService->settings = $settings;
        $emailService->sender_domains = $this->parseSenderDomains($request->input('sender_domains'));
        $emailService->is_default = $isDefault;
        $emailService->save();

        return redirect()->route('sendportal.email_services.index');
    }

    /**
     * Convert a comma- or whitespace-separated input string into a deduped,
     * lowercase array of sender domains.
     */
    private function parseSenderDomains(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        $parts = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($parts)
            ->map(fn ($d) => strtolower(trim((string) $d)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @throws Exception
     */
    public function delete(int $emailServiceId): RedirectResponse
    {
        if (!config('sendportal-host.email_services.editable', true)) {
            return redirect()->route('sendportal.email_services.index')
                ->withErrors(__('You do not have permission to delete email services.'));
        }

        $emailService = $this->emailServices->find(Sendportal::currentWorkspaceId(), $emailServiceId, ['campaigns']);

        if ($emailService->in_use) {
            return redirect()->back()->withErrors(__("You cannot delete an email service that is currently used by a campaign or automation."));
        }

        $this->emailServices->destroy(Sendportal::currentWorkspaceId(), $emailServiceId);

        return redirect()->route('sendportal.email_services.index');
    }

    public function emailServicesTypeAjax($emailServiceTypeId): JsonResponse
    {
        $emailServiceType = $this->emailServices->findType($emailServiceTypeId);

        $view = view()
            ->make('sendportal::email_services.options.' . strtolower($emailServiceType->name))
            ->render();

        return response()->json([
            'view' => $view
        ]);
    }
}
