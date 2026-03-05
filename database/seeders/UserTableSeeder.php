<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        DB::table('user_permissions')->truncate();

        $thisIP = gethostbyname(gethostname());

        $user = new User;
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->password = 'admin';
        $user->created_ip = $thisIP;
        $user->last_ip = $thisIP;
        $user->save();

        UserPermission::create([
            'user_id' => $user->id,
            'solder_full' => true,
        ]);
    }
}
