<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;

function test_hello()
{
    return 'hello world';
}


function file_save_path($fileName, $disk = 'export')
{
    $path = Storage::disk($disk)->getAdapter()->getPathPrefix();
    return $path.$fileName;
}

function get_server_Name()
{
    $hostname = gethostname();
    return $hostname.'-'.gethostbyname($hostname);
}

/**
 * 通过用户ID|用户账号获取用户信息
 * @param  string  $userId
 * @return array
 * @author dwj
 */
function getUserInfo($param = '')
{
    if (empty($param)) {
        return [];
    }
    return User::query()->where('id', $param)->orWhere('username', $param)->select()->first()->toArray();
}

/**
 * 整理时间字段返回值
 * @param  string  $dateItem
 * @return string
 * @author dwj
 */
function formatDateItem($dateItem = '')
{
    if (empty($dateItem) || $dateItem == '0000-00-00' || $dateItem == '0000-00-00 00:00:00') {
        return '';
    } else {
        return $dateItem;
    }
}

/**
 * 下划线转驼峰
 * @param $str
 * @return string|string[]|null
 * @author dwj
 */
function convertUnderline($str)
{
    $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
        return strtoupper($matches[2]);
    }, $str);
    return $str;
}

/**
 * 驼峰转下划线
 * @param $str
 * @return string
 * @author dwj
 */
function humpToLine($str)
{
    $str = str_replace("_", "", $str);
    $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
        return '_'.strtolower($matches[0]);
    }, $str);
    return ltrim($str, "_");
}

