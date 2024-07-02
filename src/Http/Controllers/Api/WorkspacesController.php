<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use App\Http\Requests\Workspaces\WorkspaceStoreRequest;
use App\Http\Requests\Workspaces\WorkspaceUpdateRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\ApiTokenRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\WorkspaceStorageUpdateRequest;
use Sendportal\Base\Http\Resources\Workspace as WorkspaceResource;
use Sendportal\Base\Repositories\EmailServiceTenantRepository;
use Sendportal\Base\Repositories\UsersRepository;
use Sendportal\Base\Repositories\WorkspacesRepository;
use Illuminate\Http\Response;

class WorkspacesController extends Controller
{
    /** @var WorkspacesRepository */
    private $workspaces;
    private ApiTokenRepository $apiTokensRepo;
    private UsersRepository $usersRepository;

    /**
     * @param WorkspacesRepository $workspaces
     * @param ApiTokenRepository $apiTokensRepo
     * @param EmailServiceTenantRepository $emailServiceTenantRepository
     */
    public function __construct(
        WorkspacesRepository $workspaces,
        UsersRepository $usersRepository,
        ApiTokenRepository $apiTokensRepo,
        EmailServiceTenantRepository $emailServiceTenantRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->workspaces = $workspaces;
        $this->apiTokensRepo = $apiTokensRepo;
        $this->emailServiceTenantRepository = $emailServiceTenantRepository;
    }

    /**
     * @throws Exception
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $workspaces = $this->workspaces->workspacesForUser($request->user());
        return WorkspaceResource::collection($workspaces);
    }

    /**
     * @throws Exception
     */
    public function createOrUpdate(WorkspaceStorageUpdateRequest $request)
    {
        $data = $request->all();
        $email = Arr::get($request, 'email');
        $res = User::where('email', $email)->withTrashed()->first();

        if ($res === null) {
            return DB::transaction(function () use ($data) {
                /** @var User $user */
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($data['password']),
                ]);

                // Create a new workspace and attach as owner.
                $workspace = $this->handle($user, $data['workspace_name'], Workspace::ROLE_MEMBER);
                $newToken = Str::random(32);
                $apiToken = $this->apiTokensRepo->store(
                    $workspace->id,
                    ['api_token' => $newToken]
                );

                $settings = [
                    "name" => "SES Traking",
                    "key" => env("SES_SERVICE_KEY"),
                    "secret" => env("SES_SERVICE_SECRET"),
                    "region" => env("SES_SERVICE_REGION"),
                    "configuration_set_name" => env("SES_SERVICE_CONFIGURATION_SET_NAME")
                ];

                $this->emailServiceTenantRepository->store($workspace->id, [
                    'name' => $data['workspace_name'],
                    'type_id' => 1,
                    'settings' => $settings,
                ]);

                $user->workspace = $workspace;
                $user->token = $apiToken;
                return $user;
            });
        } else {
            $user = User::withTrashed()->find($res->id)->restore();
            $user = $this->usersRepository->findBy('email', $email);
            $user->password = Hash::make($data['password']);
            $user->save();
            $workspaces = $this->workspaces->findBy('owner_id', $user->id);
            $workspaces->name=$data['workspace_name'];
            $workspaces->save();
            $apiToken = $this->apiTokensRepo->findBy( $workspaces->id,'workspace_id', $workspaces->id);
            $user->workspaces = $workspaces;
            $user->token = $apiToken;

            return $user;
        }
    }

    private function handle(User $user, string $workspaceName, ?string $role = null): Workspace
    {
        return DB::transaction(function () use ($user, $workspaceName, $role) {
            /** @var Workspace $workspace */
            $workspace = $this->workspaces->store([
                'name' => $workspaceName,
                'owner_id' => $user->id,
            ]);

            if (!$user->onWorkspace($workspace)) {
                $workspace->users()->attach($user, ['role' => $role ?: Workspace::ROLE_MEMBER]);
            }

            return $workspace;
        });
    }

    public function destroy(Request $request)
    {
        $email = Arr::get($request, 'email');
        $res = User::where('email', $email);
        $resul = $res->delete();
        if ($resul > 0) {
            return [
                "status" => true,
                "message" => "Xoá user thành công",
                "user_email" => $email
            ];
        } else {
            return [
                "status" => false,
                "message" => "User đã xoá.",
                "user_email" => $email,
                "error" => "ERROR"
            ];
        }
    }
}
