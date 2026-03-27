<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Subscribers;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Sendportal\Base\Events\SubscriberAddedEvent;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\SubscriberRequest;
use Sendportal\Base\Models\UnsubscribeEventType;
use Sendportal\Base\Repositories\IndustryTenantRepository;
use Sendportal\Base\Repositories\LevelTenantRepository;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Repositories\SkillTenantRepository;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Sendportal\Base\Repositories\TagTenantRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscribersController extends Controller
{
    /** @var SubscriberTenantRepositoryInterface */
    private $subscriberRepo;

    /** @var TagTenantRepository */
    private $tagRepo;
    private $locationTenantRepository;
    private $skillRepo;
    private $industryRepo;
    private $levelRepo;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscriberRepo,
        TagTenantRepository $tagRepo,
        LocationTenantRepository $locationTenantRepository,
        SkillTenantRepository $skillRepo,
        IndustryTenantRepository $industryRepo,
        LevelTenantRepository $levelRepo
    ) {
        $this->subscriberRepo = $subscriberRepo;
        $this->tagRepo = $tagRepo;
        $this->locationTenantRepository = $locationTenantRepository;
        $this->skillRepo = $skillRepo;
        $this->industryRepo = $industryRepo;
        $this->levelRepo = $levelRepo;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscribers = $this->subscriberRepo->paginate(
            $workspaceId,
            'updated_atDesc',
            ['tags', 'locations', 'skills', 'industries', 'levels'],
            100,
            request()->all()
        )->withQueryString();
        $tags = $this->tagRepo->pluck($workspaceId, 'name', 'id');
        $locations = $this->locationTenantRepository->pluck($workspaceId, 'name', 'id');
        $skills = $this->skillRepo->pluck($workspaceId, 'name', 'id');
        $industries = $this->industryRepo->pluck($workspaceId, 'name', 'id');
        $levels = $this->levelRepo->pluck($workspaceId, 'name', 'id');

        return view('sendportal::subscribers.index', compact('subscribers', 'tags', 'locations', 'skills', 'industries', 'levels'));
    }

    /**
     * @throws Exception
     */
    public function create(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $tags = $this->tagRepo->pluck($workspaceId);
        $locations = $this->locationTenantRepository->pluck($workspaceId);
        $skills = $this->skillRepo->pluck($workspaceId);
        $industries = $this->industryRepo->pluck($workspaceId);
        $levels = $this->levelRepo->pluck($workspaceId);
        $selectedTags = [];
        $selectedLocations = [];
        $selectedSkills = [];
        $selectedIndustries = [];
        $selectedLevels = [];

        return view(
            'sendportal::subscribers.create',
            compact('tags', 'locations', 'skills', 'industries', 'levels', 'selectedTags', 'selectedLocations', 'selectedSkills', 'selectedIndustries', 'selectedLevels')
        );
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberRequest $request): RedirectResponse
    {
        $data = $request->all();
        $data['unsubscribed_at'] = $request->has('subscribed') ? null : now();
        $data['unsubscribe_event_id'] = $request->has('subscribed') ? null : UnsubscribeEventType::MANUAL_BY_ADMIN;

        $subscriber = $this->subscriberRepo->store(Sendportal::currentWorkspaceId(), $data);

        event(new SubscriberAddedEvent($subscriber));

        return redirect()->route('sendportal.subscribers.index');
    }

    /**
     * @throws Exception
     */
    public function show(int $id): View
    {
        $subscriber = $this->subscriberRepo->find(
            Sendportal::currentWorkspaceId(),
            $id,
            ['tags', 'locations', 'skills', 'industries', 'levels', 'messages.source']
        );

        return view('sendportal::subscribers.show', compact('subscriber'));
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscriber = $this->subscriberRepo->find($workspaceId, $id);
        $tags = $this->tagRepo->pluck($workspaceId);
        $locations = $this->locationTenantRepository->pluck($workspaceId);
        $skills = $this->skillRepo->pluck($workspaceId);
        $industries = $this->industryRepo->pluck($workspaceId);
        $levels = $this->levelRepo->pluck($workspaceId);
        $selectedTags = $subscriber->tags->pluck('name', 'id');
        $selectedLocations = $subscriber->locations->pluck('name', 'id');
        $selectedSkills = $subscriber->skills->pluck('name', 'id');
        $selectedIndustries = $subscriber->industries->pluck('name', 'id');
        $selectedLevels = $subscriber->levels->pluck('name', 'id');

        return view('sendportal::subscribers.edit', compact(
            'subscriber', 'tags', 'locations', 'skills', 'industries', 'levels',
            'selectedTags', 'selectedLocations', 'selectedSkills', 'selectedIndustries', 'selectedLevels'
        ));
    }

    /**
     * @throws Exception
     */
    public function update(SubscriberRequest $request, int $id): RedirectResponse
    {
        $subscriber = $this->subscriberRepo->find(Sendportal::currentWorkspaceId(), $id);
        $data = $request->validated();

        // updating subscriber from subscribed -> unsubscribed
        if (!$request->has('subscribed') && !$subscriber->unsubscribed_at) {
            $data['unsubscribed_at'] = now();
            $data['unsubscribe_event_id'] = UnsubscribeEventType::MANUAL_BY_ADMIN;
        } // updating subscriber from unsubscribed -> subscribed
        elseif ($request->has('subscribed') && $subscriber->unsubscribed_at) {
            $data['unsubscribed_at'] = null;
            $data['unsubscribe_event_id'] = null;
        }

        if (!$request->has('tags')) {
            $data['tags'] = [];
        }
        if (!$request->has('locations')) {
            $data['locations'] = [];
        }
        if (!$request->has('skills')) {
            $data['skills'] = [];
        }
        if (!$request->has('industries')) {
            $data['industries'] = [];
        }
        if (!$request->has('levels')) {
            $data['levels'] = [];
        }

        $this->subscriberRepo->update(Sendportal::currentWorkspaceId(), $id, $data);

        return redirect()->route('sendportal.subscribers.index');
    }

    /**
     * @throws Exception
     */
    public function destroy($id)
    {
        $subscriber = $this->subscriberRepo->find(Sendportal::currentWorkspaceId(), $id);
        $subscriber->delete();

        return redirect()->route('sendportal.subscribers.index')->withSuccess('Subscriber deleted');
    }

    public function destroyAllByIds(Request $request)
    {
        $request->validate([
            'ids' => 'required|json'
        ]);

        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids)) {
            return redirect()->back()->withErrors('Định dạng dữ liệu không hợp lệ');
        }

        $subscribers = $this->subscriberRepo->getWhereIn(Sendportal::currentWorkspaceId(), $ids);
        $count = 0;
        foreach ($subscribers as $subscriber) {
            $subscriber->delete();
            $count++;
        }
        
        return redirect()->route('sendportal.subscribers.index')
            ->withSuccess(sprintf('Đã xóa %d subscriber thành công', $count));
    }

    /**
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     * @throws Exception
     */
    public function export(Request $request)
    {
        $subscribers = $this->subscriberRepo->all(
            Sendportal::currentWorkspaceId(),
            'id',
            ['tags', 'locations', 'skills', 'industries', 'levels']
        );

        if (!$subscribers->count()) {
            return redirect()->route('sendportal.subscribers.index')->withErrors(
                __('There are no subscribers to export')
            );
        }

        // Get selected columns from request, default to basic columns
        $columns = $request->get('columns', ['id', 'email', 'first_name', 'last_name', 'created_at']);

        // All available column definitions
        $columnMap = [
            'id' => fn($s) => $s->id,
            'hash' => fn($s) => $s->hash,
            'email' => fn($s) => $s->email,
            'first_name' => fn($s) => $s->first_name,
            'last_name' => fn($s) => $s->last_name,
            'tags' => fn($s) => $s->tags->pluck('name')->implode(', '),
            'locations' => fn($s) => $s->locations->pluck('name')->implode(', '),
            'skills' => fn($s) => $s->skills->pluck('name')->implode(', '),
            'industries' => fn($s) => $s->industries->pluck('name')->implode(', '),
            'levels' => fn($s) => $s->levels->pluck('name')->implode(', '),
            'status' => fn($s) => $s->unsubscribed_at ? 'Unsubscribed' : 'Subscribed',
            'created_at' => fn($s) => $s->created_at,
            'updated_at' => fn($s) => $s->updated_at,
        ];

        return (new FastExcel($subscribers))
            ->download(sprintf('subscribers-%s.csv', date('Y-m-d-H-m-s')), function ($subscriber) use ($columns, $columnMap) {
                $row = [];
                foreach ($columns as $col) {
                    if (isset($columnMap[$col])) {
                        $row[$col] = $columnMap[$col]($subscriber);
                    }
                }
                return $row;
            });
    }
}
