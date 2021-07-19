<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */

    public $custom = '';

    public function toArray($request)
    {
        return [
            'custom' => $this->custom,
            'data'   => parent::toArray($request)
        ];
    }

    public function setCustom($msg)
    {
        $this->custom = $msg;
        return $this;
    }
}
