<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetupCommand extends Command
{
    protected $signature = 'solder:setup
        {--username=admin : Admin username}';

    protected $description = 'Create the initial admin user for Solder';

    public function handle(): int
    {
        if (User::count() > 0) {
            $this->info('An admin user already exists. Skipping setup.');

            return self::SUCCESS;
        }

        $username = $this->option('username') ?? 'admin';

        if ($this->input->isInteractive()) {
            $email = $this->ask('Admin email');
            $password = $this->secret('Admin password');
        } else {
            $email = config('solder.initial_admin_email');
            $password = config('solder.initial_admin_password') ?: Str::random(16);
        }

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

        if (! $this->input->isInteractive() && ! config('solder.initial_admin_password')) {
            $this->warn("Generated password: {$password}");
            $this->warn('Change this password immediately after first login.');
        }

        return self::SUCCESS;
    }
}
