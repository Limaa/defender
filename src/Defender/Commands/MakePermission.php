<?php

namespace Artesaos\Defender\Commands;

use Illuminate\Console\Command;
use Artesaos\Defender\Contracts\Repositories\PermissionRepository;
use Artesaos\Defender\Contracts\Repositories\RoleRepository;
use Artesaos\Defender\Contracts\User as UserContract;

/**
 * Class MakePermission.
 */
class MakePermission extends Command
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defender:make:permission
                            {name : Name of the permission}
                            {readableName : A readable name of the permission}
                            {--user= : User id. Attach permission to user with the provided id}
                            {--role= : Role name. Attach permission to role with the provided name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a permission';

    /**
     * Create a new command instance.
     *
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
     * Execute the command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $readableName = $this->argument('readableName');
        $user = $this->option('user');
        $role = $this->option('role');

        if ($user) {
            $user = $this->findUser($user);
        }

        if ($role) {
            $role = $this->findRole($role);
        }

        $this->createPermission($name, $readableName, $user, $role);
    }

    /**
     * Create permission.
     *
     * @param string $name
     * @param string $readableName
     *
     * @return \Artesaos\Defender\Permission
     */
    protected function createPermission($name, $readableName, $user, $role)
    {
        // permissionRepository->create() throwsException when permission already exists
        $permission = $this->permissionRepository->create($name, $readableName);
        $this->info('Permission created successfully');

        if ($user) {
            $user->attachPermission($permission);
            $this->info('Permission attached successfully to user');
        }
        if ($role) {
            $role->attachPermission($permission);
            $this->info('Permission attached successfully to role');
        }
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

    /**
     * @param string $roleName
     * @return \Artesaos\Defender\Role
     * @throws \Exception
     */
    protected function findRole($roleName)
    {
        if ($role = $this->roleRepository->findByName($roleName)) {
            return $role;
        }
        throw new \Exception('Role Not Found');
    }
}
