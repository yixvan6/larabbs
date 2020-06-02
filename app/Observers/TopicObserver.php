<?php

namespace App\Observers;

use App\Models\Topic;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function saving(Topic $topic)
    {
        // 过滤文章内容，防止 xss 注入
        $topic->body = clean($topic->body, 'user_topic_body');

        $topic->excerpt = make_excerpt($topic->body);
    }

    // 数据入库后，确保 $topic 的 id 始终有值
    public function saved(Topic $topic)
    {
        // 翻译 slug
        if (! $topic->slug) {
            dispatch(new TranslateSlug($topic));
        }
    }

    public function deleted(Topic $topic)
    {
        // 删除话题后连带删除对应回复
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}
