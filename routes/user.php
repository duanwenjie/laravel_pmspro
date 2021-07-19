<?php
$router = app('router');
$router->namespace('\App\Http\Controllers\Api')->group(function (\Illuminate\Routing\Router $router) {
    //用户模块
    $router->group(['prefix'=>'User','namespace'=>'User'], function ($router) {
        //用户导入导出列表
        $router->post('importExportList', 'UserController@importExportList')->name(\App\Tools\RouteName::ROUTE_USER_IMPORT_EXPORT_LIST);
    });
});
