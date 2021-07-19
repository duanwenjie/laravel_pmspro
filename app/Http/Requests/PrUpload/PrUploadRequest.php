<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/27
 * Time: 10:08 上午
 */

namespace App\Http\Requests\PrUpload;

use App\Http\Requests\Request;

class PrUploadRequest extends Request
{
    public function rules()
    {
        $rules = [];
        $routeName = $this->route()->getName();
        switch ($routeName) {
            case 'prupload.poBatchUpload.save':
            case 'prupload.poBatchUpload.cancel':
            case 'prupload.poBatchUpload.updateError':
                $rules = [
                    'data.list'      => 'required|array',
                    'data.list.*.id' => 'required',
                ];
                break;
            case 'prupload.poBatchUpload.import':
            case 'prupload.poBatchUpload.importSave':
                $rules = [
                    'data.fileName' => 'required',
                    'data.fileUrl'  => 'required',
                ];
                break;
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'data.list'      => 'PR单数据',
            'data.list.*.id' => 'PR单号',
            'data.fileName'  => '上传文件名称',
            'data.fileUrl'   => '上传文件URL',
        ];
    }
}
