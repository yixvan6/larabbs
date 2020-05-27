<?php

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\User;
use App\Models\Category;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        $user_ids = User::pluck('id');
        $category_ids = Category::pluck('id');

        $topics = factory(Topic::class, 100)->make()
                    ->each(function ($topic) use ($user_ids, $category_ids)
        {
            $topic->user_id = $user_ids->random();
            $topic->category_id = $category_ids->random();
        });

        Topic::insert($topics->toArray());
    }
}
