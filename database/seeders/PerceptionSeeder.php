<?php

namespace Database\Seeders;

use App\Models\Perception;
use Illuminate\Database\Seeder;

class PerceptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Dating & Relationships',
                'slug' => 'dating-relationships',
                'description' => 'How to deal with dating, relationships, and sexuality.',
            ],
            [
                'name' => 'Ambition',
                'slug' => 'ambition',
                'description' => 'Career, business, ambition.',
            ],
            [
                'name' => 'Learning',
                'slug' => 'learning',
                'description' => '',
            ],
            [
                'name' => 'Collaboration & Leadership',
                'slug' => 'collaboration-leadership',
                'description' => 'Working in groups, communities, tribes, and leading.',
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => '',
            ], [
                'name' => 'Self',
                'slug' => 'self',
                'description' => 'Who are you?',
            ], [
                'name' => 'Hobbies',
                'slug' => 'hobbies',
                'description' => '',
            ],

        ];

        foreach ($data as $datum) {
            Perception::firstOrCreate($datum);
        }
    }
}
