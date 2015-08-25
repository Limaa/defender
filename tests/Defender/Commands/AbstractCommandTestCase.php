<?php

namespace Artesaos\Defender\Testing\Commands;

use Artesaos\Defender\Testing\AbstractTestCase;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;
use Artesaos\Defender\Contracts\User as UserContract;
use Artesaos\Defender\Contracts\Repositories\RoleRepository;
use Mockery as m;

abstract class AbstractCommandTestCase extends AbstractTestCase
{
    /**
     * PermissionRepository.
     *
     * @var m\Mock
     */
    protected $permissionRepository;

    /**
     * UserContract.
     *
     * @var  m\Mock
     */
    protected $user;

    /**
     * RoleRepository.
     *
     * @var m\Mock
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
}
