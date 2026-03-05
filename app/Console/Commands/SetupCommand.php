<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Console\Command;

class SetupCommand extends Command
{
    protected $signature = 'solder:setup
        {--email= : Admin email address}
        {--password= : Admin password}
        {--username=admin : Admin username}';

    protected $description = 'Create the initial admin user for Solder';

    public function handle(): int
    {
        if (User::count() > 0) {
            $this->info('An admin user already exists. Skipping setup.');

            return self::SUCCESS;
        }

        $username = $this->option('username') ?? 'admin';

        $email = $this->option('email')
            ?? (! $this->input->isInteractive() ? 'admin@admin.com' : $this->ask('Admin email'));

        $password = $this->option('password')
            ?? (! $this->input->isInteractive() ? 'admin' : $this->secret('Admin password'));

        $user = new User;
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $user->created_ip = gethostbyname(gethostname());
        $user->save();

        UserPermission::create([
            'user_id' => $user->id,
            'solder_full' => true,
        ]);

        $this->info("Admin user '{$username}' created successfully.");

        if (! $this->option('password') && ! $this->input->isInteractive()) {
            $this->warn('Using default password "admin" — change it immediately!');
        }

        return self::SUCCESS;
    }
}
