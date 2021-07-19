<?php
$router = app('router');
$router->namespace('\App\Http\Controllers\Api')->middleware('auth:sanctum')->group(function (\Illuminate\Routing\Router $router) {
    $router->group(['prefix'=>'Server','namespace'=>'Server'], function ($router) {
        $router->post('UserInfo', 'UserController@info')->name(\App\Tools\RouteName::ROUTE_SERVER_USER_INFO);
    });

    // 接收WMS占用库存、离位库存、SKU库存数据
    $router->group(['prefix' => 'Server','namespace' => 'Server'], function ($router) {
        $router->post('updateSkuUseQty', 'WmsStockController@updateSkuUseQty')->name(\App\Tools\RouteName::ROUTE_MRP_WMS_STOCK_SKU_USE_QTY);
        $router->post('updateSkuLeaveQty', 'WmsStockController@updateSkuLeaveQty')->name(\App\Tools\RouteName::ROUTE_MRP_WMS_STOCK_SKU_LEAVE_QTY);
        $router->post('updateSkuActualStockQty', 'WmsStockController@updateSkuActualStockQty')->name(\App\Tools\RouteName::ROUTE_MRP_WMS_ACTUAL_STOCK_SKU_QTY);
    });

    // 接收PMS PR单相关数据
    $router->group(['prefix' => 'Server','namespace' => 'Server'], function ($router) {
        $router->post('cancelPr', 'PmsController@cancelPr')->name(\App\Tools\RouteName::PR_PMS_CANCEL_PR);
        $router->post('receivePrData', 'PmsController@receivePrData')->name(\App\Tools\RouteName::PR_PMS_RECEIVE_PR_DATA);
    });
});
