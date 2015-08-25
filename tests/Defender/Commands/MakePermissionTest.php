<?php

namespace Artesaos\Defender\Testing\Commands;

use Artesaos\Defender\Exceptions\PermissionExistsException;
use Artesaos\Defender\Role;
use Artesaos\Defender\Permission;
use Mockery as m;
use Carbon\Carbon;

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

    /**
     * Throw RuntimeException when temporaryValue is provided and expire is not.    
     */
    public function testCommandShouldThrowRuntimeExceptionWhenTemporaryValueIsProvidedAndExpireIsNot()
    {
        $this->setExpectedException(\RuntimeException::class);

        $this->artisan('defender:make:permission', [
            'name'              => 'a.permission',
            'readableName'      => 'Just a permission',
            '--user'            => 1,
            '--temporaryValue'  => true,
            '--expires'         => null,
        ]);
    }

    /**
     * Throw RuntimeException when temporaryValue is not provided and expire is.
     */
    public function testCommandShouldThrowRuntimeExceptionWhenTemporaryValueIsNotProvidedAndExpireIs()
    {
        $this->setExpectedException(\RuntimeException::class);

        $this->artisan('defender:make:permission', [
            'name'              => 'a.permission',
            'readableName'      => 'Just a permission',
            '--user'            => 1,
            '--temporaryValue'  => null,
            '--expires'         => 'tomorrow',
        ]);
    }

    /**
     * Throw RuntimeException when User or Role are not provided.
     */
    public function testCommandShouldThrowRuntimeExceptionWhenMakingTemporaryPermissionAndUserOrRoleAreNotProvided()
    {
        $this->setExpectedException(\RuntimeException::class);

        $this->artisan('defender:make:permission', [
            'name'              => 'a.permission',
            'readableName'      => 'Just a permission',
            '--temporaryValue'  => true,
            '--expires'          => 'tomorrow',
        ]);
    }

    /**
     * Creating temporary Permission to User.
     */
    public function testCommandShouldMakeTemporaryPermissionToUser()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';
        $userId = 1;
        $temporaryValue = true;
        $expire = 'tomorrow';

        $permission = m::mock(Permission::class);

        $this->permissionRepository
            ->shouldReceive('create')
            ->once()
            ->with($permissionName, $permissionReadableName)
            ->andReturn($permission);

        $this->user->shouldReceive('findById')->once()->with($userId)->andReturnSelf();

        $this->user->shouldReceive('attachPermission')->once()->with($permission, [
            'value' => $temporaryValue,
            'expires' => new Carbon($expire),
        ]);

        $this->artisan('defender:make:permission', [
            'name'              => $permissionName,
            'readableName'      => $permissionReadableName,
            '--user'            => $userId,
            '--temporaryValue'  => $temporaryValue,
            '--expires'         => $expire,
        ]);
    }

    /**
     * Creating temporary Permission to Role.
     */
    public function testCommandShouldMakeTemporaryPermissionToRole()
    {
        $permissionName = 'a.permission';
        $permissionReadableName = 'Just a permission';
        $roleName = 'Admin';
        $temporaryValue = true;
        $expire = 'tomorrow';

        $role = m::mock(Role::class);
        $permission = m::mock(Permission::class);

        $this->permissionRepository
            ->shouldReceive('create')
            ->once()
            ->with($permissionName, $permissionReadableName)
            ->andReturn($permission);

        $this->roleRepository->shouldReceive('findByName')->once()->with($roleName)->andReturn($role);

        $role->shouldReceive('attachPermission')->once()->with($permission, [
            'value' => $temporaryValue,
            'expires' => new Carbon($expire),
        ]);

        $this->artisan('defender:make:permission', [
            'name'              => $permissionName,
            'readableName'      => $permissionReadableName,
            '--role'            => $roleName,
            '--temporaryValue'  => $temporaryValue,
            '--expires'         => $expire,
        ]);
    }
}
