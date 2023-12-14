<?php

namespace Database\Seeders;

use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reactions = ['like', 'love', 'haha', 'wow', 'angry', 'care', 'sad'];
        foreach ($reactions as $name) {
            $reaction = Reaction::createFromName($name);
            $reaction->save();
        }
    }
}
