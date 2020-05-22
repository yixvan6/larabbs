<?php

namespace App\Handlers;

use Illuminate\Support\Str;

class ImageUploadHandler
{
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg']; // 只允许上传的图片后缀

    public function save($file, $folder, $file_prefix)
    {
        // 获取文件后缀，因从剪贴板粘贴时后缀为空
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // 先判断图片后缀是否符合
        if (! in_array($extension, $this->allowed_ext)) {
            return false;
        }

        // 文件夹切割能让查找效率更高
        $folder_name = "uploads/images/$folder/" . date('Ym/d', time());

        //实际存储路径
        $upload_path = public_path() . '/' . $folder_name;

        // 拼接文件名
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        // 保存
        $file->move($upload_path, $filename);

        return ['path' => config('app.url') . "/$folder_name/$filename"];
    }
}
