<?php
$router = app('router');
$router->namespace('\App\Http\Controllers\Api')->group(function (\Illuminate\Routing\Router $router) {
    //基础模块
    $router->group(['prefix'=>'Common','namespace'=>'Common'], function ($router) {
        $router->post('dict', 'DictController@index')->name(\App\Tools\RouteName::ROUTE_COMMON_MODULE_DICT);
    });
});
