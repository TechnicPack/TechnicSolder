<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();

        $thisIP = gethostbyname(gethostname());
        $testuser = User::create([
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'created_ip' => $thisIP,
            'last_ip' => $thisIP,
            'created_by_user_id' => 1,
        ]);

        DB::table('user_permissions')->truncate();

        UserPermission::create([
            'user_id' => $testuser->id,
            'solder_full' => true,
        ]);
    }
}
