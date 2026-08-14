<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Gudang',
            'email' => 'gudang@wmf.com',
            'password' => Hash::make('gudang123'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'Bagian Keuangan',
            'email' => 'keuangan@wmf.com',
            'password' => Hash::make('keuangan123'),
            'role_id' => 3
        ]);

        User::create([
            'name' => 'Direktur',
            'email' => 'direktur@wmf.com',
            'password' => Hash::make('direktur123'),
            'role_id' => 4
        ]);
    }
}
