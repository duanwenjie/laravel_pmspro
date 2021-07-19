<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use App\Tools\ApiCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class YksFileSystem
{
    public static function upload($filename, $disk = 'export', $needOutURL = false ,$needSendToFileSystem = false)
    {
        if($needSendToFileSystem){
            $uploadUrl = config('host.yks_file_system.'.config('app.env'));
            $filePath = file_save_path($filename, $disk);
            $response = Http::attach(
                'file',
                file_get_contents($filePath),
                $filename
            )->retry(3, 1000)->timeout(25)->post($uploadUrl)->json();
            if (!isset($response['state']) || $response['state'] != ApiCode::SUCCESS) {
                throw new InvalidRequestException('调用文件服务器失败,'.$response['msg'] ?? '');
            }
            if ($needOutURL) {
                return $response['data'][0]['cnPath'];
            }
            Storage::disk($disk)->delete($filename);
            return $response['data'][0]['path'];
        }else{
           return Storage::disk($disk)->url($filename);
        }

    }
}
