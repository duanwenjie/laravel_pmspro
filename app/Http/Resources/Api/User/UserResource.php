<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case 'demo.test.list':
                return [
                    'username' => $this->username,
                    'nickname' => $this->nickname,
                    'status'   => $this->status_desc,
                ];
                break;
            case 'demo.test.list_with_relation':
                return [
                    'username'            => $this->username,
                    'nickname'            => $this->nickname,
                    'importExportRecords' => UserImportExportRecordResource::collection($this->importExportRecords),
                ];
                break;

        }
    }
}
