<?php

namespace App\Models\Traits;

use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;

trait ActiveUser
{
    // 临时存放用户数据
    protected $users = [];

    // 配置信息
    protected $topic_weight = 4; // 话题的权重
    protected $reply_weight = 4; // 回复的权重
    protected $pass_days = 7; // 取几天内的数据
    protected $user_num = 6; // 活跃用户取多少个

    // 缓存配置
    protected $cache_key = 'active_users';
    protected $cache_expire_in_seconds = 65 * 60;

    public function getActiveUsers()
    {
        // 优先从缓存中取，如果没有就用回调函数中的代码取出，并缓存
        return \Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        $active_users = $this->calculateActiveUsers();
        $this->cacheActiveUsers($active_users);
    }

    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // 按照得分排序
        $users = \Arr::sort($this->users, function ($user) {
            return $user['score'];
        });

        $users = array_reverse($users, true); // 倒序
        $users = array_slice($users, 0, $this->user_num, true);

        // 新建一个空集合
        $active_users = collect();

        foreach ($users as $id => $value) {
            $user = $this->find($id);

            // 如果用户存在
            if ($user) {
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    private function calculateTopicScore()
    {
        $topic_users = Topic::query()
                            ->select(\DB::raw('user_id, count(*) as topic_count'))
                            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                            ->groupBy('user_id')
                            ->get();

        // 计算话题得分
        foreach ($topic_users as $value) {
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    private function calculateReplyScore()
    {
        $reply_users = Reply::query()
                            ->select(\DB::raw('user_id, count(*) as reply_count'))
                            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                            ->groupBy('user_id')
                            ->get();

        // 计算回复得分
        foreach ($reply_users as $value) {
            $reply_score = $value->reply_count * $this->reply_weight;
            if (isset($this->users[$value->user_id])) {
                $this->users[$value->user_id]['score'] += $reply_score;
            } else {
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    private function cacheActiveUsers($active_users)
    {
        // 将数据放入缓存
        \Cache::put($this->cache_key, $active_users, $this->cache_expire_in_seconds);
    }
}
