<?php

namespace Artesaos\Defender\Commands;

use Artesaos\Defender\Contracts\User as UserContract;
use Artesaos\Defender\Role;

/**
 * Class MakePermission.
 */
class MakePermission extends AbstractCommand
{
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
     * @param string        $name
     * @param string        $readableName
     * @param UserContract  $user
     * @param Role          $role
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
     * @param string $roleName
     *
     * @throws \Exception
     *
     * @return Role
     */
    protected function findRole($roleName)
    {
        if ($role = $this->roleRepository->findByName($roleName)) {
            return $role;
        }
        throw new \Exception('Role Not Found');
    }
}
