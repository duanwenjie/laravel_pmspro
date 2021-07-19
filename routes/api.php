<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
require base_path('routes/user.php');
require base_path('routes/common.php');
require base_path('routes/server.php');
require base_path('routes/mrp.php');
require base_path('routes/prupload.php');
require base_path('routes/v1.php');


$router = app('router');
$router->namespace('\App\Http\Controllers\Api')->group(function (\Illuminate\Routing\Router $router) {
    $router->group(['prefix'=>'Demo','namespace'=>'Demo'], function ($router) {
        $router->middleware(['throttle:10,1'])->any('TestIndex', 'TestController@index')->name('demo.test.index');
        $router->any('TestList', 'TestController@list')->name('demo.test.list');
        $router->any('TestListWithRelation', 'TestController@listWithRelation')->name('demo.test.list_with_relation');
        $router->any('TestExport', 'TestController@export')->name('demo.test.export');
        $router->any('TestExportCsv', 'TestController@exportCsv')->name('demo.test.export_csv');
        $router->any('TestImport', 'TestController@import')->name('demo.test.import');
        $router->any('TestAsyncExport', 'TestController@asyncExport')->name('demo.test.async_export');
        $router->any('TestAsyncImport', 'TestController@asyncImport')->name('demo.test.async_import');
        $router->any('TestMotan', 'TestController@motan')->name('demo.test.motan');
        $router->any('TestLock', 'TestController@lock')->name('demo.test.lock');
    });
});
