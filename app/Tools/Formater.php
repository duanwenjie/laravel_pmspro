<?php


namespace App\Tools;

use Throwable;

class Formater
{
    /**
     * 格式化搜索时间数组
     * @param $searchTime
     * @return array
     */
    public static function searchTime($searchTime)
    {
        return array_map(function ($n) {
            return date('Y-m-d H:i:s', substr($n, 0, 10));
        }, $searchTime);
    }

    /**
     * 格式化多值
     * @param $value
     * @return array
     */
    public static function multiValue($value)
    {
        return array_values(array_unique(array_map(
            'trim',
            array_filter(is_array($value) ? $value : explode(',', $value))
        )));
    }


    /**
     * 格式化字典
     * @param $map
     * @return array
     */
    public static function formatDict($map)
    {
        $data = [];
        foreach ($map as $key => $value) {
            $data[] = [
                'code' => $key,
                'name' => $value,
            ];
        }
        return $data;
    }


    public static function formatDingTalkMsg($title, $content, Throwable $exception = null, $short = false)
    {
        $appName = config('app.name');
        $appEnv = config('app.env');
        $payload = "【$appName 监控报警】"."\n\n";
        $payload .= '【报警信息】'.$title."\n";
        $payload .= '【报警时间】'.date('Y-m-d H:i:s')."\n";
        $payload .= '【服务器】'.get_server_Name()."\n";
        $payload .= '【所在环境】'.$appEnv."\n";
        if ($exception) {
            $content = $exception->getMessage();
        }
        $payload .= '【报警详情】'.(is_array($content) ? json_encode(
            $content,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) : $content)."\n";
        if ($exception) {
            $payload .= '【异常文件&行数】'."FILE:{$exception->getFile()} ,LINE: {$exception->getLine()} 行 "."\n";
            if (!$short) {
                $payload .= '【异常栈信息】'." {$exception->getTraceAsString()} "."\n";
            }
        }
        return $payload;
    }

    //获取批量插入sql
    public static function sqlInsertAll($table, $data, $update = '')
    {
        if (empty($data) || empty($data[0])) {
            return false;
        }
        $key = array_keys($data[0]);
        foreach ($data as $v) {
            $vTemp = array();
            foreach ($key as $vv) {
                $vTemp[] = addslashes($v[$vv]);
            }
            $tempData[] = "('".implode("','", $vTemp)."')";
        }
        $tempUpdate = array();
        if ($update && is_array($update)) {
            foreach ($update as $vv) {
                if (in_array($vv, $key)) {
                    $tempUpdate[] = "{$vv}=VALUES({$vv})";
                }
            }
        }
        $sql = "INSERT INTO {$table} (".implode(',', $key).") VALUES ".implode(",", $tempData);
        if ($update && !empty($tempUpdate)) {
            $sql .= " ON DUPLICATE KEY UPDATE ".implode(',', $tempUpdate);
        }
        return $sql;
    }
}
