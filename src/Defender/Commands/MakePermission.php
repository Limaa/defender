<?php

namespace Artesaos\Defender\Commands;

use Artesaos\Defender\Contracts\User as UserContract;
use Artesaos\Defender\Role;
use Carbon\Carbon;

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
                            {--role= : Role name. Attach permission to role with the provided name}
                            {--temporaryValue= : Value (true|false). To temporarily remove or add a permission to a user or role}
                            {--expires= : Expires. When the temporary permission expires. A Carbon accepted string}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a permission';

    /**
     * @var array
     */
    protected $args;

    /**
     * Execute the command.
     */
    public function handle()
    {
        $this->args = [
            'name'           => $this->argument('name'),
            'readableName'   => $this->argument('readableName'),
            'user'           => $this->option('user'),
            'role'           => $this->option('role'),
            'temporaryValue' => $this->option('temporaryValue'),
            'expires'        => $this->option('expires'),
        ];

        $this->checkArgs();

        $this->createPermission();

        $this->attachPermission();
    }

    /**
     * Creates the permission.
     */
    protected function createPermission()
    {
        // permissionRepository->create() throwsException when permission already exists
        $this->args['permission'] = $this->permissionRepository->create($this->args['name'], $this->args['readableName']);
        $this->info('Permission created successfully');
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

    /**
     * Check the arguments.
     */
    protected function checkArgs()
    {
        if ($this->args['temporaryValue'] xor $this->args['expires']) {
            throw new \RuntimeException('Both arguments --temporaryValue and --expires must be provided to create a temporary permission.');
        }

        if (($this->args['temporaryValue'] && $this->args['expires']) && !($this->args['user'] || $this->args['role'])) {
            throw new \RuntimeException('No user or role provided to create a temporary permission.');
        }

        if ($this->args['user']) {
            $this->args['user'] = $this->findUser($this->args['user']);
        }

        if ($this->args['role']) {
            $this->args['role'] = $this->findRole($this->args['role']);
        }
    }

    /**
     * Attach permission to user or role with temporary when needed.
     * @return [type] [description]
     */
    protected function attachPermission()
    {
        $options = [];

        if ($this->args['temporaryValue'] && $this->args['expires']) {
            $options = [
                'value'     => $this->args['temporaryValue'],
                'expires'   => new Carbon($this->args['expires']),
            ];
        }

        if ($this->args['user']) {
            $this->args['user']->attachPermission($this->args['permission'], $options);
            $this->info('Permission attached successfully to user');
        }

        if ($this->args['role']) {
            $this->args['role']->attachPermission($this->args['permission'], $options);
            $this->info('Permission attached successfully to role');
        }
    }
}
