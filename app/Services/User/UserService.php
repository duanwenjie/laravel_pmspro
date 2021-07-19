<?php


namespace App\Services\User;

use App\Exports\UsersExport;
use App\Imports\UserImport;
use App\Models\User;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UserService
{

    /**
     * Desc:导入用户
     * @param $requestParam
     */
    public function import($requestParam)
    {
        $importExportRecord = $requestParam['import_export_record'];
        Storage::disk('export')->put(
            $importExportRecord->file_name,
            file_get_contents($importExportRecord->file_upload_url)
        );
        $filePath = file_save_path($importExportRecord->file_name, 'export');
        (new UserImport())->import($filePath);
        $importExportRecord->update(
            [
                'status'       => UserImportExportRecord::STATUS_SUCCESS,
                'completed_at' => Carbon::now(),
                'result'       => '处理成功'
            ]
        );
        Storage::disk('export')->delete($importExportRecord->file_name);
    }

    public function export($requestParam)
    {
        $importExportRecord = $requestParam['import_export_record'];
        //拿请求中的数据 此处容易犯错
        $requestData = $requestParam['data'] ?? [];
        $filename = '成员信息-'.date('ymdHis').'.xlsx';
        $builder = User::query()->filter($requestData);
        Excel::store(new UsersExport($builder), $filename, 'export');
        $importExportRecord->update(
            [
                'status'            => UserImportExportRecord::STATUS_SUCCESS,
                'file_download_url' => YksFileSystem::upload($filename),
                'completed_at'      => Carbon::now(),
                'result'            => '处理成功'
            ]
        );
    }

    /**
     * 导入导出字典
     * @return array
     * @author jip
     * @time 2020/12/28 17:29
     */
    public function getImportExportDict()
    {
        return [
            'type'   => Formater::formatDict(UserImportExportRecord::$typesMap),
            'module' => Formater::formatDict(UserImportExportRecord::$moduleMap),
            'status' => Formater::formatDict(UserImportExportRecord::$statusMap),
        ];
    }
}
