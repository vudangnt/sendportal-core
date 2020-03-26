<?php

namespace Tests;

use Collective\Html\FormFacade;
use Laravel\Ui\UiServiceProvider;
use Sendportal\Base\Models\Segment;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Models\Workspace;
use Sendportal\Base\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Sendportal\Base\SendportalBaseServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            UiServiceProvider::class,
            SendportalBaseServiceProvider::class
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();
        $this->withExceptionHandling();
        $this->withFactories(__DIR__ . '/../database/factories');

        $this->artisan('migrate')->run();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Form' => FormFacade::class
        ];
    }

    /**
     * Create a user with attached workspace.
     */
    protected function createUserWithWorkspace(): User
    {
        return factory(Workspace::class)->create()->owner;
    }

    /**
     * Create a user with attached workspace, returning both workspace and user.
     */
    protected function createUserAndWorkspace(): array
    {
        $workspace = factory(Workspace::class)->create();

        return [$workspace, $workspace->owner];
    }

    /**
     * Log in the given user.
     */
    protected function loginUser(User $user): void
    {
        auth()->login($user);
    }

    protected function createSegment(User $user): Segment
    {
        return factory(Segment::class)->create([
            'workspace_id' => $user->currentWorkspace()->id,
        ]);
    }

    protected function createSubscriber(User $user): Subscriber
    {
        return factory(Subscriber::class)->create([
            'workspace_id' => $user->currentWorkspace()->id,
        ]);
    }

    public function assertLoginRedirect(TestResponse $response): void
    {
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function createUserAndLogin(array $states = [], array $overrides = []): User
    {
        $user = factory(User::class)->states($states)->create($overrides);
        $this->actingAs($user);

        return $user;
    }

    public function createWorkspaceUser(Workspace $workspace, array $overrides = []): User
    {
        $user = factory(User::class)->create($overrides);
        $workspace->users()->attach($user, ['role' => Workspace::ROLE_MEMBER]);

        return $user;
    }
}
