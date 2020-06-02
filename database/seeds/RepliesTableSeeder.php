<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;
use App\Models\User;
use App\Models\Topic;

class RepliesTableSeeder extends Seeder
{
    public function run()
    {
        $user_ids = User::pluck('id');
        $topic_ids = Topic::pluck('id');

        $replies = factory(Reply::class, 1500)->make()
                        ->each(function ($reply) use ($user_ids, $topic_ids)
        {
            $reply->user_id = $user_ids->random();
            $reply->topic_id = $topic_ids->random();
        });

        Reply::insert($replies->toArray());
    }
}
