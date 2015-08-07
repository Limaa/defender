<?php

namespace Artesaos\Defender\Testing;

use \Mockery as m;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;
use Artesaos\Defender\Contracts\Repositories\RoleRepository;
use Artesaos\Defender\Contracts\User as UserContract;
use Artesaos\Defender\Role;

class CommandsTest extends AbstractTestCase
{
    /**
     * PermissionRepository
     *
     * @var m\Mock $permissionRepository
     */
    protected $permissionRepository;

    /**
     * UserContract
     *
     * @var  m\Mock $user
     */
    protected $user;

    /**
     * RoleRepository
     *
     * @var m\Mock $roleRepository
     */
    protected $roleRepository;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->permissionRepository = m::mock(PermissionRepository::class);
        $this->app->instance(PermissionRepository::class, $this->permissionRepository);

        $this->roleRepository = m::mock(RoleRepository::class);
        $this->app->instance(RoleRepository::class, $this->roleRepository);

        $this->user = m::mock(UserContract::class);
        $this->app->instance(UserContract::class, $this->user);
    }


    /**
     * @inheritdoc
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [
            'Artesaos\Defender\Providers\DefenderServiceProvider',
        ];
    }

    /**
     * Creating a Permission.
     */
    public function testCommandShouldMakeAPermission()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';

        $this->permissionRepository->shouldReceive('create')->once()->with($permissionName, $permissionReadableName);

        $this->artisan('defender:make:permission',
            ['name' => $permissionName, 'readableName' => $permissionReadableName]);
    }

    /**
     * Creating a Permission to User
     */
    public function testCommandShouldMakeAPermissionToUser()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';
        $userId = 1;

        $this->permissionRepository->shouldReceive('create')->once()->with($permissionName, $permissionReadableName);
        $this->user->shouldReceive('findById')->once()->with($userId)->andReturnSelf();
        $this->user->shouldReceive('attachPermission')->once();

        $this->artisan('defender:make:permission',
            ['name' => $permissionName, 'readableName' => $permissionReadableName, '--user' => $userId]);
    }

    /**
     * Creating a Permission to Role
     */
    public function testCommandShouldMakeAPermissionToRole()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';
        $roleName = 'Admin';

        /** @var m\Mock $role */
        $role = m::mock(Role::class);

        $this->permissionRepository->shouldReceive('create')->once()->with($permissionName, $permissionReadableName);
        $this->roleRepository->shouldReceive('findByName')->once()->with($roleName)->andReturn($role);
        $role->shouldReceive('attachPermission')->once();

        $this->artisan('defender:make:permission',
            ['name' => $permissionName, 'readableName' => $permissionReadableName, '--role' => $roleName]);
    }

    /**
     * Creating a Role
     */
    public function testCommandShouldMakeARole()
    {
        $roleName = 'Admin';

        $this->roleRepository->shouldReceive('create')->once()->with($roleName);

        $this->artisan('defender:make:role', ['name' => $roleName]);
    }

    /**
     * Creating a Role to User
     */
    public function testCommandShouldMakeARoleToUser()
    {
        $roleName = 'Admin';
        $userId = 1;

        $this->roleRepository->shouldReceive('create')->once()->with($roleName);
        $this->user->shouldReceive('findById')->once()->with($userId)->andReturnSelf();
        $this->user->shouldReceive('attachRole')->once();

        $this->artisan('defender:make:role', ['name' => $roleName, '--user' => $userId]);

    }
}
