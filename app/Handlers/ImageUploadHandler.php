<?php

namespace App\Handlers;

use Illuminate\Support\Str;
use Image;

class ImageUploadHandler
{
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg']; // 只允许上传的图片后缀

    public function save($file, $folder, $file_prefix, $max_width = false)
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

        // 如果设置了宽度限制，就对非 gif 图片进行裁剪
        if ($max_width && $extension != 'gif') {
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return ['path' => config('app.url') . "/$folder_name/$filename"];
    }

    public function reduceSize($file, $max_width)
    {
        $image = Image::make($file);

        // 调整大小
        $image->resize($max_width, null, function ($constraint) {
            // 高度根据宽度等比例缩放
            $constraint->aspectRatio();

            // 防止图片变大
            $constraint->upsize();
        });

        // 最后对修改的图片进行保存
        $image->save();
    }
}
