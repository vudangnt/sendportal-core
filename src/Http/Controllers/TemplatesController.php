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
