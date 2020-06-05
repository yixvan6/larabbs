<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait LastActivedTime
{
    protected $hash_prefix = 'larabbs_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        $now = Carbon::now()->toDateTimeString();

        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        $field = $this->getHashField();

        Redis::hSet($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());

        $data = Redis::hGetAll($hash);

        // 遍历，并同步到数据库
        foreach ($data as $user_id => $actived_at) {
            $user_id = str_replace($this->field_prefix, '', $user_id);

            // 当用户存在时才更新到数据库
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心的存储，即已同步，即可删除
        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        $field = $this->getHashField();

        // 优先选择 Redis 缓存中的数据，没有再从数据库取
        $datetime = Redis::hGet($hash, $field) ?: $value;

        // 将返回的时间转为 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 如果没有，则使用注册时间
            return $this->created_at;
        }
    }

    protected function getHashFromDateString($date)
    {
        return $this->hash_prefix . $date;
    }

    protected function getHashField()
    {
        return $this->field_prefix . $this->id;
    }
}
