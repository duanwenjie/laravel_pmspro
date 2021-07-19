<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpBaseSkuCoreSfExport;
use App\Exports\Mrp\MrpBaseSkuCoreV3Export;
use App\Imports\Mrp\MrpBaseSkuCoreImport;
use App\ModelFilters\Mrp\MrpBaseSkuCoreSfFilter;
use App\Models\Mrp\MrpBaseSkuCore;
use App\Models\MrpBaseData\MrpBaseSkuInfoList;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

class MrpBaseSkuCoreSfService
{
    protected $downloadLimitRows = 300000;

    /**
     * 列表
     * @param  int  $type
     * @return mixed
     */
    public function list($type = MrpBaseSkuCore::TYPE_SF)
    {
        $request = request();
        return $this->builder($type)->orderByDesc('id')
            ->paginate($request->input('perPage'));
    }

    /**
     * Desc:获取前端查询条件
     * @param $type
     * @return mixed
     */
    private function builder($type, $requestArr = '')
    {
        if (!$requestArr) {
            $requestArr = request()->input('data', []);
        }
        $columns = [
            'id',
            'sku',
            'stock_way',
            'sales_status',
            'buffer_stock_cycle',
            'active_stock_cycle',
            'fixed_stock_num',
            'supply_cycle',
            'stock_advance_cycle',
            'stock_cycle',
            'remark',
            'sku_mark',
            'created_user',
            'created_at',
            'updated_user',
            'updated_at'
        ];
        return MrpBaseSkuCore::query()->where(
            'type',
            '=',
            $type
        )->select($columns)->with('skuInfo')->filter(
            $requestArr,
            MrpBaseSkuCoreSfFilter::class
        );
    }

    /**
     * 导出
     * @param $requestParam
     * @return mixed
     * @throws InvalidRequestException
     */
    public function export($requestParam)
    {
        ini_set('memory_limit', '4096M');
        $importExportRecord = $requestParam['import_export_record'];
        //拿请求中的数据 此处容易犯错
        $requestData = $requestParam['data'] ?? [];
        $filename = '备货关系表(sf)-'.date('ymdHis').'.csv';
        $builder = $this->builder(MrpBaseSkuCore::TYPE_SF, $requestData);
//        Excel::store(new MrpBaseSkuCoreSfExport($builder), $filename, 'export',\Maatwebsite\Excel\Excel::CSV);
        $filePath = file_save_path($filename);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'id',
            'SKU',
            'sku中文名称',
            '备货方式',
            '销售状态',
            '主仓库id',
            '特定备货数量',
            '安全库存天数',
            '交期',
            '产品标识',
            '创建人',
            '计划员',
            '创建时间',
            '最后更新人',
            '最后更新时间'
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,//id
                    $report->sku,//SKU
                    $report->skuInfo['cn_name']??'',//中文名称
                    $report->stock_way_name,//备货方式描述
                    $report->sales_status_name,//销售状态描述
                    $report->skuInfo['main_warehouseid']??'',//主仓库id
                    $report->fixed_stock_num,//特定备货数量
                    $report->buffer_stock_cycle,//安全库存天数
                    $report->supply_cycle,//交期
                    $report->sku_mark,//产品标识
                    $report->created_user,//创建人
                    $report->skuInfo['planner_nick']??'',//计划员
                    $report->created_at,//创建时间
                    $report->updated_user,//最后更新人
                    $report->updated_at,//最后更新时间
                ];
            }
            $writer->insertAll($formatData);
        });

        $importExportRecord->update(
            [
                'status'            => UserImportExportRecord::STATUS_SUCCESS,
                'file_download_url' => YksFileSystem::upload($filename),
                'completed_at'      => Carbon::now(),
                'result'            => '处理成功'
            ]
        );
    }

    public function importSf($requestParam = [])
    {
        $importExportRecord = $requestParam['import_export_record'];
        Storage::disk('export')->put(
            $importExportRecord->file_name,
            file_get_contents($importExportRecord->file_upload_url)
        );
        $filePath = file_save_path($importExportRecord->file_name, 'export');
        (new MrpBaseSkuCoreImport(MrpBaseSkuCore::TYPE_SF))->import($filePath);
        $importExportRecord->update(
            [
                'status'       => UserImportExportRecord::STATUS_SUCCESS,
                'completed_at' => Carbon::now(),
                'result'       => '处理成功'
            ]
        );
        Storage::disk('export')->delete($importExportRecord->file_name);
    }

    /**
     * Desc:
     * @param  array  $requestParam
     * @throws Exception
     * @throws InvalidRequestException
     */
    public function importSfByCsv($requestParam = [])
    {
        $importExportRecord = $requestParam['import_export_record'];
        Storage::disk('export')->put(
            $importExportRecord->file_name,
            file_get_contents($importExportRecord->file_upload_url)
        );
        $filePath = file_save_path($importExportRecord->file_name, 'export');
        $this->doImportByCsv($filePath, MrpBaseSkuCore::TYPE_SF);
        $importExportRecord->update(
            [
                'status'       => UserImportExportRecord::STATUS_SUCCESS,
                'completed_at' => Carbon::now(),
                'result'       => '处理成功'
            ]
        );
        Storage::disk('export')->delete($importExportRecord->file_name);
    }

    /**
     * Desc:
     * @param $filePath
     * @param $type
     * @throws Exception
     */
    private function doImportByCsv($filePath, $type)
    {
        $reader = Reader::createFromPath($filePath, 'r');
        $reader->setHeaderOffset(0);
        //检查编码
        $header = $reader->getHeader();
        foreach ($header as $key => $v) {
            $encode = mb_detect_encoding($v, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
            if ($encode == 'EUC-CN') {
                $reader->addStreamFilter('convert.iconv.GBK/UTF-8');
                break;
            }
        }
        $records = $reader->getRecords();
        $temp = [];
        $status = array_flip(MrpBaseSkuCore::$salesStatus);
        $stockWay = array_flip(MrpBaseSkuCore::$stockWay);
        Log::info('process validate start');
        foreach ($records as $line => $record) {
//            // 表单验证
//                $validator = Validator::make($record, [
//                    'SKU'    => 'required',
//                    '备货方式'   => ['required', Rule::in(array_values(MrpBaseSkuCore::$stockWay))],
//                    '销售状态'   => ['required', Rule::in(array_values(MrpBaseSkuCore::$salesStatus))],
//                    '安全库存天数' => 'required',
//                    '交期'     => 'required',
//                    '补货天数'   => 'required',
//                    '活动库存天数' => 'required',
//                    '特定备货数量' => 'required',
//                    '产品标识'   => 'required',
//                ]);
//                if ($validator->fails()) {
//                    throw new InvalidRequestException("第{$line}行异常,".join('|', $validator->errors()->all()));
//                }
            $salesStatusRecord = trim($record['销售状态']);
            $stockWayRecord = trim($record['备货方式']);
            $temp[] = [
                'sku'                 => $record['SKU'],
                'stock_way'           => $stockWay[$stockWayRecord]?? '',
                'sales_status'        => $status[$salesStatusRecord]?? '',
                'buffer_stock_cycle'  => $record['安全库存天数']??0,
                'supply_cycle'        => $record['交期']??0,
                'stock_cycle'         => $record['补货天数']??0,
                'active_stock_cycle'  => $record['活动库存天数']??0,
                'fixed_stock_num'     => $record['特定备货数量']??0,
                'sku_mark'            => $record['产品标识']??'',
                'remark'              => $record['备注'] ?? '',
                'type'                => $type,
                'created_user'        => Auth::user()->nickname,
                'updated_user'        => Auth::user()->nickname,
                'stock_advance_cycle' => $record['安全库存天数'] + $record['交期'] + $record['活动库存天数'],
            ];
        }
        Log::info('process validate end');
        $chunkDataList = array_chunk($temp, 3000);
        foreach ($chunkDataList as $chunkData) {
            Log::info('process');
            $baseSku =  array_column($chunkData,'sku');
            $baseSkuList = MrpBaseSkuInfoList::query()->whereIn('sku', $baseSku)->pluck('sku')->toArray();
            $updateData = [];
            foreach ($chunkData as $item) {
                if (in_array($item['sku'], $baseSkuList)) {
                    $updateData[] = $item;
                }
            }
            Log::info('process insert start');
            MrpBaseSkuCore::query()->upsert($updateData, ['sku', 'type'], [
                'sku',
                'stock_way',
                'sales_status',
                'buffer_stock_cycle',
                'supply_cycle',
                'stock_cycle',
                'active_stock_cycle',
                'fixed_stock_num',
                'sku_mark',
                'remark',
                'stock_advance_cycle',
                'updated_user'
            ]);
            unset($chunkData,$updateData,$baseSku);
            Log::info('process insert end');
        }
    }

    public function importV3($requestParam = [])
    {
        $importExportRecord = $requestParam['import_export_record'];
        Storage::disk('export')->put(
            $importExportRecord->file_name,
            file_get_contents($importExportRecord->file_upload_url)
        );
        $filePath = file_save_path($importExportRecord->file_name, 'export');
        (new MrpBaseSkuCoreImport(MrpBaseSkuCore::TYPE_V3))->import($filePath);
        $importExportRecord->update(
            [
                'status'       => UserImportExportRecord::STATUS_SUCCESS,
                'completed_at' => Carbon::now(),
                'result'       => '处理成功'
            ]
        );
        Storage::disk('export')->delete($importExportRecord->file_name);
    }

    /**
     * Desc:
     * @param  array  $requestParam
     * @throws InvalidRequestException
     * @throws Exception
     *
     */
    public function importV3ByCsv($requestParam = [])
    {
        $importExportRecord = $requestParam['import_export_record'];
        Log::info('init');
        Storage::disk('export')->put(
            $importExportRecord->file_name,
            file_get_contents($importExportRecord->file_upload_url)
        );
        Log::info('init url');
        $filePath = file_save_path($importExportRecord->file_name, 'export');
        $this->doImportByCsv($filePath, MrpBaseSkuCore::TYPE_V3);
        Log::info('end');
        $importExportRecord->update(
            [
                'status'       => UserImportExportRecord::STATUS_SUCCESS,
                'completed_at' => Carbon::now(),
                'result'       => '处理成功'
            ]
        );
        Storage::disk('export')->delete($importExportRecord->file_name);
    }

    /**
     * 导出
     * @return mixed
     * @throws InvalidRequestException
     */
    public function exportV3($requestParam)
    {
        ini_set('memory_limit', '4096M');
        $importExportRecord = $requestParam['import_export_record'];
        //拿请求中的数据 此处容易犯错
        $requestData = $requestParam['data'] ?? [];
        $filename = '备货关系表(v3)-'.date('ymdHis').'.csv';
        $builder = $this->builder(MrpBaseSkuCore::TYPE_V3, $requestData);
//        Excel::store(new MrpBaseSkuCoreV3Export($builder), $filename, 'export',\Maatwebsite\Excel\Excel::CSV);
        $filePath = file_save_path($filename);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            '自增ID',
            'SKU',
            'sku中文名称',
            '备货方式',
            '销售状态',
            '主仓库id',
            '价格',
            '安全库存天数',
            '活动库存天数',
            '特定备货数量',
            '交期',
            '库内库存天数',
            '补货天数',
            '备注',
            '产品标识',
            '创建人',
            '创建时间',
            '最后更新人',
            '最后更新时间',
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,//id
                    $report->sku,//SKU
                    $report->skuInfo['cn_name'] ?? '',//中文名称
                    $report->stock_way_name,//备货方式描述
                    $report->sales_status_name,//销售状态描述
                    $report->skuInfo['main_warehouseid'] ?? '',//主仓库id
                    $report->sku_price,//价格
                    $report->buffer_stock_cycle,//安全库存天数
                    $report->active_stock_cycle,//活动库存天数
                    $report->fixed_stock_num,//特定备货数量
                    $report->supply_cycle,//交期
                    $report->stock_advance_cycle,//库内库存天数
                    $report->stock_cycle,//补货天数
                    $report->remark,//备注
                    $report->sku_mark,//产品标识
                    $report->created_user,//创建人
                    $report->created_at,//创建时间
                    $report->updated_user,//最后更新人
                    $report->updated_at,//最后更新时间
                ];
            }
            $writer->insertAll($formatData);
        });
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
     * 字典
     * @return array
     * @author dwj
     */
    public function getMrpBaseSkuCoreDict()
    {
        return [
            'stockWay'    => Formater::formatDict(MrpBaseSkuCore::$stockWay),
            'salesStatus' => Formater::formatDict(MrpBaseSkuCore::$salesStatus),
        ];
    }
}
