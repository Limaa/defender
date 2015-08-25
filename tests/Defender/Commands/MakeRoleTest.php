<?php

namespace Artesaos\Defender\Testing\Commands;

use Artesaos\Defender\Exceptions\RoleExistsException;

class MakeRoleTest extends AbstractCommandTestCase
{
    /**
     * Throw RuntimeException when no Name provided.
     */
    public function testCommandShouldThrowRuntimeExceptionWhenNoNameIsProvided()
    {
        $this->roleRepository->shouldNotReceive('create');
        $this->setExpectedException(\RuntimeException::class);
        $this->artisan('defender:make:role');
    }

    /**
     * Throw RoleExistsException when Role already in database.
     */
    public function testCommandShouldThrowRoleExistsExceptionWhenRoleIsAlreadySavedInDatabase()
    {
        $roleName = 'Admin';

        $this->roleRepository->shouldReceive('create')
            ->once()
            ->with($roleName)
            ->andThrow(RoleExistsException::class);

        $this->setExpectedException(RoleExistsException::class);

        $this->artisan('defender:make:role',
            ['name' => $roleName]);
    }

    /**
     * Creating a Role.
     */
    public function testCommandShouldMakeARole()
    {
        $roleName = 'Admin';

        $this->roleRepository->shouldReceive('create')->once()->with($roleName);

        $this->artisan('defender:make:role', ['name' => $roleName]);
    }

    /**
     * Throw Exception when User not found.
     */
    public function testCommandShouldThrowExceptionWhenUserNotFound()
    {
        $roleName = 'Admin';
        $userId = 1;

        $this->user->shouldReceive('findById')->once()->with($userId)->andReturnNull();
        $this->roleRepository->shouldNotReceive('create');

        $this->setExpectedException(\Exception::class);

        $this->artisan('defender:make:role',
            ['name' => $roleName, '--user' => $userId]);
    }

    /**
     * Creating a Role to User.
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
