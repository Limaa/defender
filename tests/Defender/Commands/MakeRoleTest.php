<?php

namespace Artesaos\Defender\Testing\Commands;

class MakeRoleTest extends AbstractCommandTestCase
{
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
