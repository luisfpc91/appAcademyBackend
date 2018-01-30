<?php

use Illuminate\Database\Seeder;

class UserTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $tests = array(
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'avatar' => '/img/avatar.png',
                'password' => bcrypt('123456'),
                'level' => 'admin'
            ],
            [
                'name' => 'user',
                'email' => 'user@user.com',
                'avatar' => '/img/avatar.png',
                'password' => bcrypt('123456'),
                'level' => 'user'
            ]
        );

        foreach ($tests as $key) {
            DB::table('users')->insert($key);
        }

    }
}
