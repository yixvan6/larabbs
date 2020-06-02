<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function scopeOrderWith($query, $order)
    {
        switch ($order) {
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;

            // 默认用 最后回复 排序
            default:
                // 当话题有新回复时，我们会改变 reply_count 的值，就会自动触发对 updated_at 的更新
                $query->orderBy('updated_at', 'desc');
                break;
        }
    }

    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }

    public function updateReplyCount()
    {
        $this->reply_count = $this->replies->count();
        $this->save();
    }
}
