<?php

namespace App\Exceptions;

use App\Tools\ApiCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InvalidRequestException extends Exception
{
    //

    protected $data = [];

    public function __construct($message, $code = ApiCode::HTTP_BAD_REQUEST, $data = [])
    {
        parent::__construct($message, $code);

        $this->data = $data;
    }

    public function render(Request $request)
    {
        $returnData = [
            'state' => str_pad($this->code, 6, '0', STR_PAD_LEFT),
            'msg'   => $this->message,
        ];
        if (!empty($this->data)) {
            $returnData['data'] = $this->data;
        }

        return Response::json($returnData);
    }
}
