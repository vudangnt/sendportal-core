<?php

namespace Sendportal\Base\Http\Controllers\Subscribers;

use App\Imports\UsersImport;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Exception;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Rap2hpoutre\FastExcel\FastExcel;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\SubscribersImportRequest;
use Sendportal\Base\Repositories\TagTenantRepository;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Services\Subscribers\ImportSubscriberService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Sendportal\Base\Jobs\ImportSubscribersJob;
use Sendportal\Base\Jobs\TrackImportProgressJob;

class SubscribersImportController extends Controller
{
    /** @var ImportSubscriberService */
    protected $subscriberService;

    public function __construct(ImportSubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    /**
     * @throws Exception
     */
    public function show(TagTenantRepository $tagRepo, LocationTenantRepository $locationRepo): ViewContract
    {
        $tags = $tagRepo->pluck(Sendportal::currentWorkspaceId(), 'name', 'id');
        $locations = $locationRepo->pluck(Sendportal::currentWorkspaceId(), 'name', 'id');

        return view('sendportal::subscribers.import', compact('tags', 'locations'));
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function store(SubscribersImportRequest $request): RedirectResponse
    {
        // Remove memory and execution time limits
        ini_set('memory_limit', '-1');          // -1 sets memory limit to unlimited
        ini_set('max_execution_time', '0');     // 0 sets execution time to unlimited

        if ($request->file('file')->isValid()) {

            $filename = Str::random(16) . '.xslx';
            $path = $request->file('file')->storeAs('imports', $filename, 'local');

            $array = (new UsersImport)->toArray(Storage::disk('local')->path($path));
            
            $chunks = array_chunk($array[0], 500); // Xử lý mỗi lần 1000 records
            $totalChunks = count($chunks);

            // Lưu thời gian bắt đầu
            $workspaceId = Sendportal::currentWorkspaceId();

            Cache::put("import_start_time_{$workspaceId}", now(), now()->addHours(1));

            // Khởi tạo tiến trình
            TrackImportProgressJob::dispatch($workspaceId, $totalChunks, 0)
                ->onQueue('default');

            foreach ($chunks as $index => $chunk) {
                Log::info('Importing chunk ' . ($index + 1) . ' of ' . $totalChunks);
                ImportSubscribersJob::dispatch(
                    $chunk,
                    $workspaceId,
                    $request->get('tags') ?? [],
                    $request->get('locations') ?? [],
                    $index + 1,  // Chunk hiện tại
                    $totalChunks // Tổng số chunks
                )->onQueue('default');
            }

            // Xóa file tạm
            Storage::disk('local')->delete($path);


            return redirect()->route('sendportal.subscribers.index')
                ->with('success', __('Import đang được xử lý trong nền. Bạn sẽ nhận được thông báo khi hoàn tất.'));
        }

        return redirect()->route('sendportal.subscribers.index')
            ->with('errors', __('The uploaded file is not valid'));
    }

    /**
     * @param string $path
     * @return ViewErrorBag
     * @throws IOException
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     */
    protected function validateCsvContents(string $path): ViewErrorBag
    {
        $errors = new ViewErrorBag();
        $row = 1;
        (new FastExcel)->import($path, function (array $line) use ($errors, &$row) {


// Split the keys and values by the semicolon delimiter
            $keys = explode(';', array_keys($line)[0]);
            $values = explode(';', array_values($line)[0]);

            // Trim any extra whitespace from the keys and values
            $keys = array_map('trim', $keys);
            $values = array_map('trim', $values);

// Debugging: Check the length of keys and values
            if (count($keys) !== count($values)) {
                throw new Exception("Bị lỗi dữ liệu ở hàng thứ " . $row);
            }
// Combine them into an associative array
            $parsedData = array_combine($keys, $values);
            $data = Arr::only($parsedData, ['id', 'email', 'first_name', 'last_name']);
            try {
                $this->validateData($data);
            } catch (ValidationException $e) {
                $errors->put('Row ' . $row, $e->validator->errors());
            }

            $row++;
        });

        return $errors;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    protected function validateData(array $data): void
    {
        $validator = Validator::make($data, [
            'id' => 'integer',
            'email' => 'required|email:filter',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function importProgress()
    {
        $workspaceId = 1;//Sendportal::currentWorkspaceId();
        $cacheKey = "import_progress_{$workspaceId}";
        
        $progress = Cache::get($cacheKey, [
            'progress' => 0,
            'updated_at' => null
        ]);

        return response()->json($progress);
    }
}
