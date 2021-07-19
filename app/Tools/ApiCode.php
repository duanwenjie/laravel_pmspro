<?php


namespace App\Tools;

class ApiCode
{
    const SUCCESS = '000001';
    const HTTP_BAD_REQUEST = '000400';
    const HTTP_UNAUTHORIZED = '000401';
    const HTTP_FORBIDDEN = '000403';
    const HTTP_METHOD_NOT_ALLOWED = '000405';
    const HTTP_TOO_MANY_REQUESTS = '000429';
    const HTTP_UNPROCESSABLE_ENTITY = '000422';
    const HTTP_INTERNAL_SERVER_ERROR = '000500';


    public static $codeList = [
        self::SUCCESS                    => '请求成功',
        self::HTTP_BAD_REQUEST           => '请求失败',
        self::HTTP_UNAUTHORIZED          => '授权失败',
        self::HTTP_FORBIDDEN             => '禁止访问',
        self::HTTP_METHOD_NOT_ALLOWED    => '请求方法不允许',
        self::HTTP_TOO_MANY_REQUESTS     => '请求太频繁',
        self::HTTP_UNPROCESSABLE_ENTITY  => '表单提交异常',
        self::HTTP_INTERNAL_SERVER_ERROR => '系统内部错误',
    ];
}
