<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    /**
     * XXX报表
     * 入参：json
     * 输出：json
     */
    public function test()
    {
        $data = ['id' => 1, 'name' => 'test'];
        return $this->success('成功', $data);
    }
}
