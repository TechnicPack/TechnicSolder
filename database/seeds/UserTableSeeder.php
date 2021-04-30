<?php

use App\User;
use App\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $thisIP = getHostByName(getHostName());
        $testuser = User::create([
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'created_ip' => $thisIP,
            'last_ip' => $thisIP,
            'created_by_user_id' => 1
        ]);

        DB::table('user_permissions')->delete();

        UserPermission::create([
            'user_id' => $testuser->id,
            'solder_full' => true
        ]);
    }

}
