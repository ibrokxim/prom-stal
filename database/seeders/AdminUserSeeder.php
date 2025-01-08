<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'apromstal@admin.com',
            'password' => Hash::make('agdepassword'), // Хэшируем пароль
        ]);
    }
}
