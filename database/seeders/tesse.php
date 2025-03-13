<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class tesse extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Tesseract',
            'email' => 'ibrakimfal@gmail.com',
            'password' => Hash::make('adminygy'),
            'role' => 'admin',
        ]);
    }
}
