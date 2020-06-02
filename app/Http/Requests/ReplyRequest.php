<?php

namespace App\Http\Requests;

class ReplyRequest extends Request
{
    public function rules()
    {
        return [
            'content' => 'required|min:2',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => '回复需要至少 2 个字符',
            'content.min' => '回复需要至少 2 个字符',
        ];
    }
}
