<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/5
 * Time: 6:44 下午
 */

namespace App\Http\Resources\Api\Mrp;

use Illuminate\Http\Resources\Json\JsonResource;

class MrpResource extends JsonResource
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
        }
    }
}
