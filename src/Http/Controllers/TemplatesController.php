<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Requests\TemplateStoreRequest;
use Sendportal\Base\Http\Requests\TemplateUpdateRequest;
use Sendportal\Base\Models\Template;
use Sendportal\Base\Repositories\TemplateTenantRepository;
use Sendportal\Base\Services\Templates\TemplateService;
use Sendportal\Base\Traits\NormalizeTags;
use Throwable;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse as JsonResponseAlias;

class TemplatesController extends Controller
{
    use NormalizeTags;

    /** @var TemplateTenantRepository */
    private $templates;

    /** @var TemplateService */
    private $service;

    public function __construct(TemplateTenantRepository $templates, TemplateService $service)
    {
        $this->templates = $templates;
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $templates = $this->templates->paginate(Sendportal::currentWorkspaceId(), 'name', [], [],
            ['status' => 'active']);

        return view('sendportal::templates.index', compact('templates'));
    }

    public function exportJson($id)
    {
        // Find the template by ID
        $template = $this->templates->find(Sendportal::currentWorkspaceId(), $id);
        if (!$template) {
            abort(404);
        }
        // Get the data_json value
        $data = $template->data_json;

        // Define the JSON file name
        $fileName = $template->name.'.json';

        // Return the JSON data as a downloadable response
        return Response::make($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function create(): View
    {
        return view('sendportal::templates.create');
    }

    public function showImportForm(): View
    {
        return view('sendportal::templates.import');
    }

    public function importJson(Request $request)
    {
        $name = $request->file('jsonFile')->getClientOriginalName();
        $jsonDataString = file_get_contents($request->file('jsonFile')->getRealPath());

        $jsonData = json_decode($jsonDataString, true);
        // Assuming 'name' and 'content' fields are in the JSON structure
        if (!$jsonData || !isset($jsonData['body']) || !isset($jsonData['body'])) {
            return redirect()->back()->withErrors(['jsonFile' => 'Invalid JSON structure.']);
        }

        // Create a new template with the JSON data
        $data = [
            'name' => $name,
            'content' => '',
            'data_json' => $jsonDataString,
        ];

        $template = $this->service->store(Sendportal::currentWorkspaceId(), $data);

        // Redirect to the edit screen of the new template
        return view('sendportal::templates.edit', compact('template'));

    }

    /**
     * @throws Exception
     */
    public function store(TemplateStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data = $this->service->store(Sendportal::currentWorkspaceId(), $data);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function duplicate(int $id): View
    {
        $template = $this->templates->find(Sendportal::currentWorkspaceId(), $id);
        $template->name = $template->name . ' (duplicate)';
        return view('sendportal::templates.create', compact('template'));
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $template = $this->templates->find(Sendportal::currentWorkspaceId(), $id);
        return view('sendportal::templates.edit', compact('template'));
    }

    public function show(int $id): View
    {
        $template = $this->templates->find(Sendportal::currentWorkspaceId(), $id);
        return view('sendportal::templates.edit', compact('template'));
    }

    /**
     * @throws Exception
     */
    public function update(TemplateUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $data = $this->service->update(Sendportal::currentWorkspaceId(), $id, $data);
        return response()->json(['success' => true, 'data' => $data]);

    }

    /**
     * Return saved templates as JSON for Market tab
     */
    public function market(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $workspaceId = Sendportal::currentWorkspaceId();

        // Market only surfaces campaign-style templates so the listing
        // matches what marketDesign (via TemplateTenantRepository) can fetch.
        $query = Template::where('workspace_id', $workspaceId)
            ->where('kind', Template::KIND_CAMPAIGN);

        // Filter by status if the column has values
        if (\Illuminate\Support\Facades\Schema::hasColumn('sendportal_templates', 'status')) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            });
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $templates = $query->orderBy('updated_at', 'desc')
            ->select(['id', 'name', 'content', 'data_json', 'created_at', 'updated_at'])
            ->paginate(12);

        return response()->json($templates);
    }

    /**
     * Return a single template's design JSON for import
     */
    public function marketDesign(int $id): JsonResponse
    {
        $template = $this->templates->find(Sendportal::currentWorkspaceId(), $id);

        if (!$template || !$template->data_json) {
            return response()->json(['error' => 'Template not found or has no design'], 404);
        }

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'data_json' => $template->data_json,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete(Sendportal::currentWorkspaceId(), $id);

        return redirect()
            ->route('sendportal.templates.index')
            ->with('success', __('Template successfully deleted.'));
    }
}
