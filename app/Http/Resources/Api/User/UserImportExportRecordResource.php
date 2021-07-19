<?php

namespace App\Http\Resources\Api\User;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class UserImportExportRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status'          => $this->status,
            'statusDesc'      => $this->status_desc,
            'type'            => $this->type,
            'typeDesc'        => $this->type_desc,
            'fileName'        => $this->file_name,
            'module'          => $this->module,
            'moduleDesc'      => $this->module_desc,
            'fileDownloadUrl' => $this->file_download_url,
            'fileUploadUrl'   => $this->file_upload_url,
            'completedAt'     => $this->completed_at,
            'createdAt'       => $this->created_at,
            'result'          => $this->result,
        ];


        $routeName = $request->route()->getName();
        switch ($routeName) {
            //异常件列表
            case RouteName::ROUTE_USER_IMPORT_EXPORT_LIST:
                return [
                    'status'          => $this->status,
                    'statusDesc'      => $this->status_desc,
                    'type'            => $this->type,
                    'typeDesc'        => $this->type_desc,
                    'fileName'        => $this->file_name,
                    'module'          => $this->module,
                    'moduleDesc'      => $this->module_desc,
                    'fileDownloadUrl' => $this->file_download_url,
                    'fileUploadUrl'   => $this->file_upload_url,
                    'completedAt'     => $this->completed_at,
                    'createdAt'       => $this->created_at,
                    'result'          => $this->result,
                ];
                break;
        }
    }
}
