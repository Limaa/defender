<?php

namespace Artesaos\Defender\Commands;

use Illuminate\Console\Command;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;
use Artesaos\Defender\Contracts\Repositories\RoleRepository;
use Artesaos\Defender\Contracts\User as UserContract;

abstract class AbstractCommand extends Command
{
    /**
     * Defender Permissions Repository.
     *
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * Defender Roles Repository.
     *
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * User which implements UserContract.
     *
     * @var UserContract
     */
    protected $user;

    /**
     * @param PermissionRepository $permissionRepository
     * @param RoleRepository       $roleRepository
     * @param UserContract         $user
     */
    public function __construct(PermissionRepository $permissionRepository, RoleRepository $roleRepository, UserContract $user)
    {
        parent::__construct();

        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
        $this->user = $user;
    }

    /**
     * @param int $userId
     * @return UserContract
     * @throws \Exception
     */
    protected function findUser($userId)
    {
        if ($user = $this->user->findById($userId)) {
            return $user;
        }
        throw new \Exception('User Not Found');
    }
}
