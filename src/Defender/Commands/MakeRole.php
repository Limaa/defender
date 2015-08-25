<?php

namespace Artesaos\Defender\Commands;

use Artesaos\Defender\Contracts\User as UserContract;

/**
 * Class MakeRole.
 */
class MakeRole extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defender:make:role
                            {name : Name of the role}
                            {--user= : User id. Attach role to user with the provided id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a role';

    /**
     * Execute the command.
     */
    public function handle()
    {
        $roleName = $this->argument('name');
        $user = $this->option('user');

        if ($user) {
            $user = $this->findUser($user);
        }

        $this->createRole($roleName, $user);
    }

    /**
     * Create role.
     *
     * @param string        $roleName
     * @param UserContract  $user
     */
    protected function createRole($roleName, $user)
    {
        // roleRepository->create() throwsException when role already exists
        $role = $this->roleRepository->create($roleName);
        $this->info('Permission created successfully');

        if ($user) {
            $user->attachRole($role);
            $this->info('Role attached successfully to user');
        }
    }
}
