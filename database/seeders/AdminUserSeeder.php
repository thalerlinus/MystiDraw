<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'linus.bewerbung@hotmail.com';
        $user = User::firstOrCreate([
            'email' => $email
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'), // Bitte später ändern
        ]);

        if (!$user->is_admin) {
            $user->is_admin = true;
            $user->save();
        }
    }
}
