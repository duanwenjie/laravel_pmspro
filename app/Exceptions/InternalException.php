<?php

namespace App\Exceptions;

use App\Tools\ApiCode;
use Exception;
use Illuminate\Support\Facades\Response;

class InternalException extends Exception
{
    protected $msgForUser;

    public function __construct($message, $code = ApiCode::HTTP_INTERNAL_SERVER_ERROR, $msgForUser = '系统内部错误')
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render()
    {
        return Response::json([
            'state' => str_pad($this->code, 6, '0', STR_PAD_LEFT),
            'msg'   => $this->msgForUser,
        ]);
    }
}
