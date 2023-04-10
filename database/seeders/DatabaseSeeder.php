<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'admin',
        //     'email' => 'admin@test.com',
        //     'password' => '$2y$10$a0YlBxkVQt0da/EwI5wtheJ3FsAgkDwLOQsabpiPmLgxbSSBoY/aC',
        //     'role' => 'SuperAdmin'
        // ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Programming',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Design',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'UI/UX',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Game',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Database',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Machine Learning',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Networking',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Security',
        ]);

        \App\Models\Tags::factory()->create([
            'name' => 'Web',
        ]);
        \App\Models\Tags::factory()->create([
            'name' => 'Negara',
        ]);

        Post::factory()->count(30)->create();
    }
}
