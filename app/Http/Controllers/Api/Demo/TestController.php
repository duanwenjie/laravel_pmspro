<?php


namespace App\Http\Controllers\Api\Demo;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\UserResource;
use App\Imports\UserImport;
use App\Models\User;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\MrpBaseData\MrpOtherReport1Service;
use App\Services\User\UserService;
use App\Tools\CacheSet;
use App\Tools\Client\YksFileSystem;
use App\Tools\Motan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    public function index(Request $request)
    {
        //时间处理
//        $day = Carbon::now()->format('Y-m-d');
//        $day = Carbon::parse('2020-01-01')->addDay()->format('Y-m-d');
//        var_dump($day);
//        exit;
//        $user = User::query()->where('username', 'lichun1')->first();
//        $user->notify(new UserNotify('哈哈哈'.time()));
////        Cache::put(CacheSet::COMMON_ACCOUNTS_KEY_VALUE, ['fo'=>111], CacheSet::TTL_MAP[CacheSet::COMMON_ACCOUNTS_KEY_VALUE]);
////        Log::info('111');
    }

    public function list()
    {
        $request = request();

//        dd(Auth::user()->toArray());
        //取数据
//        $user = User::query()->find(1);
//        $users = User::query()->limit(10)->get()->pluck('nickname');
//        $users1 = User::query()->limit(10)->get()->keyBy('id');
//        $users2 = User::query()->limit(10)->select('nickname', 'username','status')->get();
//        $users3 = User::query()->select('nickname', 'username')->paginate($request->input('perPage'));
//        $users4 = User::query()->filter($request->input('data',[]))->paginate();
//        return $this->successForResourcePage(UserResource::collection($users4));

//        return $this->successForResourcePage(UserResource::collection($users4));
        //取出来是collection对象，可以用 collection对象 进行处理
//        var_dump($users,$users1,$users2);exit;


//       return $this->successForResource(new UserResource($user));

//       return $this->successForResource( UserResource::collection($users2));

//       return $this->successForResource( new UsersResourceCollection($users2) );

//       return $this->successForResourcePage(UserResource::collection($users3));

//         return $this->successForResourcePage( (new UsersResourceCollection($users3))->setCustom('11111') );

//
//        DB::transaction(function (){
//
//            $user = User::query()->create([
//                'username'=>'12222211' ,
//                'nickname'=> '2222222',
//            ]);
        ////            throw  new InvalidRequestException('111');
//        });
//
//

        //       $times = 60;
//       for($i=0 ;$i<$times;$i++){
//           $this->dispatch(new TestJob($i));
//       }

//        dispatch(new Test('bar'));
//
//        exit;


//       $users =  Auth::user();
//       var_dump($users);

        //通知
//       $user = User::query()->where('username','lichun1')->first();
//       $user->notify(new UserNotify('哈哈哈'.time()));

        Cache::put(CacheSet::COMMON_ACCOUNTS_KEY_VALUE, 'ssss', CacheSet::TTL_MAP[CacheSet::COMMON_ACCOUNTS_KEY_VALUE]);
        Log::info('111');
    }


    public function listWithRelation(Request $request)
    {

        //whereHas
        $users4 = User::query()
            ->filter($request->input('data', []))
            ->with(['importExportRecords'])
            ->paginate($request->input('prePage', 20));
        return $this->successForResourcePage(UserResource::collection($users4));
    }

    //导入
    public function import()
    {
        $request = request();
        $fileName = $request->input('data.fileName');
        $fileUrl = $request->input('data.fileUrl');
        Storage::disk('export')->put($fileName, file_get_contents($fileUrl));
        $filePath = file_save_path($fileName, 'export');
        (new UserImport())->import($filePath);
        Storage::disk('export')->delete($fileName);
    }

    //导出
    public function export()
    {
        //保存文件
//        $request = request();
        $builder = User::query(); //builder 逻辑可以和查询列表共用
        $fileName = "用户列表.xlsx";
        Excel::store(new UsersExport($builder), $fileName, 'export');
        $url = YksFileSystem::upload($fileName);
        return $this->success('xxx', $url);

        //直接下载
//        $request = request();
//        $builder = User::query();
//        $name = "用户列表.xlsx";
//        return Excel::download(new UsersExport($builder), $name);
    }

    //导出csv（针对复杂的导出）
    public function exportCsv()
    {
        $builder = User::query();
        $fileName = "用户列表.csv";
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = ['用户名', '姓名'];
        $writer->insertOne($header);
        $builder->chunkById(100, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $user) {
                $formatData[] = [
                    $user->username,
                    $user->nickname,
                ];
            }
            $writer->insertAll($formatData);
        });
        $url = YksFileSystem::upload($fileName);
        return $this->success('xxx', $url);
//         echo  $url;
//        $writer->output($name);
    }


    public function asyncExport()
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_USER,
            '用户列表.xlsx',
            [new UserService(), 'export']
        );
    }


    public function asyncImport()
    {
        $request = request();
        $fileName = $request->input('data.fileName');
        $fileUrl = $request->input('data.fileUrl');
        (new ExportService())->asyncImport(
            UserImportExportRecord::MODULE_USER,
            $fileName,
            $fileUrl,
            [new UserService(), 'import']
        );
    }


    public function motan()
    {
        $url = 'motan2://172.36.6.110:8002/com.yks.urc.motan.service.api.IUrcService?group=rpc-service-group-pro';
        $params = ['lstRoleId' => ['1547543636522000240'], "operator" => 'liqiang'];
        return $this->success('查询成功', Motan::call('getRoleUser', $params, $url));
    }


    public function throttle()
    {


        // 定义一个限定并发访问频率的限流器，最多支持 100 个并发请求
        Redis::funnel("test111")
            ->limit(100)
            ->then(function () {
            }, function () {
                // 触发并发访问上限
            });

        // 限定单位时间访问上限
        Redis::throttle("test2222")
            ->allow(1)->every(10)
            ->then(function () {
            }, function () {
                // 触发并发访问上限
            });
    }


    public function lock()
    {
        $lock = Cache::lock('foo', 10);

        if ($lock->get()) {
            // 获取锁定10秒...

            echo 111;
            $lock->release();
        } else {
            echo '重复提交';
        }

//        Cache::lock('foo')->forceRelease();
    }
}
