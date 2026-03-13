<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // library roles: reader and librarian
        $librarian = Role::firstOrCreate(['name' => 'librarian']);
        $reader = Role::firstOrCreate(['name' => 'reader']);

        // Attach librarian to test user if exists
        $test = User::where('email', 'test@example.com')->first();
        if ($test) {
            $test->roles()->syncWithoutDetaching([$librarian->id]);
        }

    }
}
