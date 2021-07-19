<?php


namespace App\Services;

use App\Jobs\AsyncImportExportJob;
use App\Models\UserImportExportRecord;
use Illuminate\Support\Facades\Auth;

class ExportService
{
    public function asyncExport($module, $filename, array $action)
    {
        $user = Auth::user();
        $importExportRecord = $user->importExportRecords()->create([
            'module'    => $module,
            'file_name' => $filename,
            'type'      => UserImportExportRecord::TYPE_EXPORT
        ]);
        $request = request();
        dispatch(new AsyncImportExportJob($importExportRecord, $request->all(), $action));
    }


    public function asyncImport($module, $filename, $filePath, array $action)
    {
        //插入队列
        $user = Auth::user();
        $importExportRecord = $user->importExportRecords()->create([
            'module'          => $module,
            'file_name'       => $filename,
            'file_upload_url' => $filePath,
            'type'            => UserImportExportRecord::TYPE_IMPORT
        ]);
        $request = request();
        dispatch(new AsyncImportExportJob($importExportRecord, $request->all(), $action));
    }
}
