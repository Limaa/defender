<?php

namespace Artesaos\Defender\Testing\Commands;

use Artesaos\Defender\Exceptions\PermissionExistsException;
use Artesaos\Defender\Role;
use Mockery as m;

class MakePermissionTest extends AbstractCommandTestCase
{
    /**
     * Throw RuntimeException when no Name provided.
     */
    public function testCommandShouldThrowRuntimeExceptionWhenNoNameIsProvided()
    {
        $this->permissionRepository->shouldNotReceive('create');
        $this->setExpectedException(\RuntimeException::class);
        $this->artisan('defender:make:permission', ['readableName' => 'Just a permission']);
    }

    /**
     * Throw RuntimeException when no ReadableName provided.
     */
    public function testCommandShouldThrowRuntimeExceptionWhenNoReadablenameIsProvided()
    {
        $this->permissionRepository->shouldNotReceive('create');
        $this->setExpectedException(\RuntimeException::class);
        $this->artisan('defender:make:permission', ['name' => 'a.permission']);
    }

    /**
     * Throw PermissionExistsException when Permission already in database.
     */
    public function testCommandShouldThrowPermissionExistsExceptionWhenPermissionIsAlreadySavedInDatabase()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';

        $this->permissionRepository->shouldReceive('create')
            ->once()
            ->with($permissionName, $permissionReadableName)
            ->andThrow(PermissionExistsException::class);

        $this->setExpectedException(PermissionExistsException::class);

        $this->artisan('defender:make:permission',
            ['name' => $permissionName, 'readableName' => $permissionReadableName]);
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
     * Throw Exception when User not found.
     */
    public function testCommandShouldThrowExceptionWhenUserNotFound()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';
        $userId = 1;

        $this->user->shouldReceive('findById')->once()->with($userId)->andReturnNull();
        $this->permissionRepository->shouldNotReceive('create');

        $this->setExpectedException(\Exception::class);

        $this->artisan('defender:make:permission',
            ['name' => $permissionName, 'readableName' => $permissionReadableName, '--user' => $userId]);
    }

    /**
     * Creating a Permission to User.
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
     * Throw Exception when Role not found.
     */
    public function testCommandShouldThrowExceptionWhenRoleNotFound()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';
        $roleName = 1;

        $this->roleRepository->shouldReceive('findByName')->once()->with($roleName)->andReturnNull();
        $this->permissionRepository->shouldNotReceive('create');

        $this->setExpectedException(\Exception::class);

        $this->artisan('defender:make:permission',
            ['name' => $permissionName, 'readableName' => $permissionReadableName, '--role' => $roleName]);
    }

    /**
     * Creating a Permission to Role.
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
}
