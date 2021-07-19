<?php

namespace App\Http\Requests\Api\Demo;

use App\Http\Requests\Request;

class TestingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $routeName = $this->route()->getName();
        switch ($routeName) {
            case 'demo.test.index':
                $rules = [
                    'data.tag'         => 'required|string',
                    'data.productType' => 'required|in:0,1,4',
                    'data.toNodeId'    => 'required|in:1,2,3'
                ];
                break;
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'data.tag'         => '标签',
            'data.productType' => '产品类型'
        ];
    }
}
