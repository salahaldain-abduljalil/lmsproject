<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        DB::table('users')->insert(
            [
                'name'=> 'Admin',
                'user_name'=> 'admin',
                'email'=> 'admin@gmail.com',
                'password'=> Hash::make(111),
                'role'=> 'admin',
                'status'=> '1',
            ]);
     DB::table('users')->insert(
        [
            'name'=> 'Instructor',
            'user_name'=> 'instructor',
            'email'=> 'instructor@gmail.com',
            'password'=> Hash::make(111),
            'role'=> 'instructor',
            'status'=> '1',
        ]
        );
        DB::table('users')->insert(
            [
                'name'=> 'User',
                'user_name'=> 'user',
                'email'=> 'user@gmail.com',
                'password'=> Hash::make(111),
                'role'=> 'user',
                'status'=> '1',
            ],
        );

    }
}
