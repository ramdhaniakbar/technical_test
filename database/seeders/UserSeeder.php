<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'nama' => 'Ramdhani Akbar',
                'email' => 'ramdhaniakbar@gmail.com',
                'password' => bcrypt('123456'),
                'npp' => '12345',
                'npp_supervisor' => '54321',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Gonalu Kaler',
                'email' => 'gonalukaler@gmail.com',
                'password' => bcrypt('123456'),
                'npp' => '38425',
                'npp_supervisor' => '-',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
