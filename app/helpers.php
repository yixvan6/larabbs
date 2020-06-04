<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}

function make_excerpt($topic_body, $length = 160)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($topic_body)));
    return Str::limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    $model_name = model_plural_name($model);

    $prefix = $prefix ? "/$prefix/" : '/';

    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    return '<a href="' . $url .'" target="_blank">' . $title . '</a>';
}

// 将模型的类名转为复数蛇形命名
function model_plural_name($model)
{
    $class_name = class_basename($model);

    $snake_name = Str::snake($class_name);

    return Str::plural($snake_name);
}
