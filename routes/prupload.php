<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/24
 * Time: 5:17 下午
 */

$router = app('router');
$router->namespace('\App\Http\Controllers\Api')->group(function (\Illuminate\Routing\Router $router) {
    $router->group(['prefix' => 'PrUpload','namespace' => 'PrUpload'], function ($router) {
        // PR单批量上传
        $router->post('import', 'PoBatchUploadController@import')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_IMPORT);
        // PR单列表
        $router->post('list', 'PoBatchUploadController@list')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_LIST);
        // PR单列表导出
        $router->post('exportList', 'PoBatchUploadController@exportList')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_EXPORT_LIST);
        // PR单保存
        $router->post('save', 'PoBatchUploadController@save')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_SAVE);
        // PR单上传保存
        $router->post('importSave', 'PoBatchUploadController@importSave')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_IMPORT_SAVE);
        // PR单撤销
        $router->post('cancel', 'PoBatchUploadController@cancel')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_CANCEL);
        // PR单批量更新异常
        $router->post('updateError', 'PoBatchUploadController@updateError')->name(\App\Tools\RouteName::PR_BATCH_UPLOAD_UPDATE_ERROR);

        // SKU进度跟进列表
        $router->post('followList', 'SkuFollowController@list')->name(\App\Tools\RouteName::SKU_FOLLOW_LIST);
        // SKU进度跟进列表导出
        $router->post('followExportList', 'SkuFollowController@exportList')->name(\App\Tools\RouteName::SKU_FOLLOW_EXPORT);
    });
});
