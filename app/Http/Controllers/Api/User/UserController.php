<?php


namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\UserImportExportRecordResource;
use App\Models\UserImportExportRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    /**
     * Desc: 获取用户导入导出数据导出
     * @return JsonResponse
     */
    public function importExportList()
    {
        $user = Auth::user();
        $request = request();
        $userImportExportRecords = UserImportExportRecord::query()
            ->filter($request->input('data', []))
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('perPage', 20));
        return $this->successForResourcePage(UserImportExportRecordResource::collection($userImportExportRecords));
    }
}
