<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/1
 * Time: 17:58
 */


namespace App\Services\Report;


use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\CancelpoExport;
use App\Exports\Mrp\MrpResultPlanV3Export;
use App\Imports\Mrp\CancelpoImport;
use App\ModelFilters\Mrp\CancelpoFilter;
use App\Models\Pms\CancelPoBt;
use App\Models\Pms\CancelPoBtDetail;
use App\Models\Pms\PurchaseorderDetail;
use App\Tools\Client\YksFileSystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CancelpoService
{
    const STATE_ARR = [
		2 => '待采购',
        3 => '审核中',
        4 => '可打印',
        5 => '已打印',
        6 => '未完全到货',
        7 => '完全到货',
        9 => '已质检',
        10 => '未完全入库',
        11 => '完全入库',
        12 => '手动完结',
        13 => '取消'
    ];
    const CANCEL_STATUS = [
        0 => '未撤销',
        1 => '已撤销',
        -1 => '撤销失败',
    ];
    const IS_SHOW = [
        0 => '不显示',
        1 => '显示',
    ];

    public $downloadLimitRows = 100000;

    /**
     * 撤销在途列表
     * @return mixed
     */
    public function getOrderLists(){
        return $this->builderOrder()->paginate(request()->input('perPage'));
    }

    /**
     * 撤销在途-导出
     */
    public function exportOrderLists(){
        ini_set('memory_limit', '4096M');
        $builder = $this->builderOrder();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."exportOrderLists.csv";
        Excel::store(new CancelpoExport($builder,request()), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    public function operateOrderLists(){
        CancelPoBt::query()->update(['is_new'=>0]);
        $builder = $this->builderOrder('operate')->groupBy('pd.sku');
        CancelPoBt::query()->insertUsing(['bt_no','time_str','range_date','sku','un_quantity','avl_sku_qty','mod_aft_v3_qty','base_num','cancel_base_num','count_cancel_num','count_cancel_days','su_cancel_num','create_user','is_new','pr_num'],$builder);
        $res = CancelPoBt::query()->select()->where('is_new','=',1);
        /*$res = array_chunk($res,3000);
        foreach ($res as $v){
            $tmpPms = array_column($v,null,'sku');
            $tmpSkus = array_keys($tmpPms);

            $tmp = DB::connection('hz')->table('stock_order_use_qty','uq')
                ->select([
                    DB::raw('s.sku_qty-uq.order_use_qty-uq.newwms_use_qty avl_sku_qty'),
                    DB::raw('if(m.avg_day_sales>0,m.avg_day_sales,0.0001) mod_aft_v3_qty'),
                ])
                ->leftJoin('stock_order as s',function($join){
                    $join->on('uq.sku','=','s.sku');
                    $join->on('uq.warehouseid','=','s.warehouseid');
                })
                ->leftJoin('mrp_sku_orig_salesdata_detail_v3 as m',function($join){
                    $join->on('uq.sku','=','m.sku');
                })
                ->whereIn('uq.sku',$tmpSkus)
                ->where('uq.warehouseid','=',3)
                ->get()->pluck('uq.sku');
            foreach ($tmpPms as $vs){
                $sku = $vs;
                $pr_num = $vs['pr_num']??0;//PR数
                $un_quantity = $vs['un_quantity']??0;//未交量
                $base_num = $vs['base_num']??0;//可销基数
                $cancel_base_num = $vs['cancel_base_num']??0;//撤销可销基数
                $count_cancel_num = 0;//计算撤销量
                $mod_aft_v3_qty = $tmp[$sku]['mod_aft_v3_qty'];
                $avl_sku_qty = $tmp[$sku]['avl_sku_qty'];
                $s = $pr_num+$un_quantity+$avl_sku_qty-$mod_aft_v3_qty*$base_num;
                if($s>=0){
                    $count_cancel_num = $s>=$un_quantity?$un_quantity:ceil($s);
                }
                $count_cancel_days = $f = $mod_aft_v3_qty != 0?round($count_cancel_num/$mod_aft_v3_qty,2):0;//撤销量可销天数
                $su_cancel_num =$count_cancel_num==$un_quantity || $f>=$cancel_base_num?$count_cancel_num:0;//建议撤销量
                CancelPoBt::query()->update([
                    'mod_aft_v3_qty'=>$mod_aft_v3_qty,
                    'avl_sku_qty'=>$avl_sku_qty,
                    'count_cancel_num'=>$count_cancel_num,
                    'count_cancel_days'=>$count_cancel_days,
                    'su_cancel_num'=>$su_cancel_num,])
                ->where('bt_no','=',$vs['bt_no']);
            }
        }
        CancelPoBtDetail::query()->update(['is_new',0]);*/
        return true;
    }

    /**
     * 撤销明细-导出
     */
    public function exportDetaillResultLists(){
        ini_set('memory_limit', '4096M');
        $builder = $this->buildDetaillResult();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."exportDetaillResultLists.csv";
        Excel::store(new CancelpoExport($builder,request()), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 撤销明细列表
     * @return mixed
     */
    public function getDetaillResultLists(){
        return $this->buildDetaillResult()->paginate(request()->input('perPage'));
    }

    /**
     * 撤销在途明细-上传
     */
    public function uploadDetaillResultLists($filePath){
       return  (new CancelpoImport)->import($filePath);
    }

    /**
     * 撤销汇总列表
     * @return mixed
     */
    public function getTotalResultLists(){
        return $this->buildTotalResult()->paginate(request()->input('perPage'));
    }

    /**
     * 撤销明细-导出
     */
    public function exportTotalResultLists(){
        ini_set('memory_limit', '4096M');
        $builder = $this->buildTotalResult();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."exportTotalResultLists.csv";
        Excel::store(new CancelpoExport($builder,request()), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 撤销在途查询
     * @return mixed
     */
    public function builderOrder($type = 'lists'){
        $request = request()->input('data', []);
        if($type == 'operate'){
            $timeStr = date('YmdHis');
            $rangeDate = $request['createDate']??'';
            $baseNum = $request['baseNum'];
            $cancelBaseNum = $request['cancelBaseNum'];
            $createUser = Auth::user()->nickname;
            $fields = [
                DB::raw("concat(pd.sku,'_',$timeStr) bt_no"),
                DB::raw("'$timeStr' time_str"),
                DB::raw("'$rangeDate' range_date"),
                'pd.sku',
                DB::raw('SUM(pd.quantity)-SUM(pd.ware_quantity) un_quantity'),
                DB::raw('0 avl_sku_qty'),
                DB::raw('0.0001 mod_aft_v3_qty'),
                DB::raw("$baseNum base_num"),
                DB::raw("$cancelBaseNum cancel_base_num"),
                DB::raw('0 count_cancel_num'),
                DB::raw('0 count_cancel_days'),
                DB::raw('0 su_cancel_num'),
                DB::raw("'$createUser' create_user"),
                DB::raw('1 is_new'),
                DB::raw('case when sum(surplus_quantity)>0 then sum(surplus_quantity) else 0 end pr_num'),
            ];
        }else{
            $fields = [
                'pd.id',
                'p.id as po',
                'pd.sku',
                'pd.quantity',
                'pd.state',
                'p.create_time',
                'pd.delivery_date',
                'pd.ware_quantity',
                DB::raw("case pd.last_ware_date when '1970-01-01 00:00:00' then '' else  pd.last_ware_date end  last_ware_date"),
                'p.warehouse_id',
                DB::raw("pd.quantity-pd.ware_quantity un_quantity"),
                'pl.pr_id',
                'p.orderer'
                ,'p.merchandiser'
            ];
        }

        return PurchaseorderDetail::query()->from('purchaseorder_detail','pd')
            ->join('purchaseorder as p',function($join){
                $join->on('pd.purchaseorder_id','=','p.id');
            })
            ->leftJoin('purchaseplan_order as po',function($join){
                $join->on('pd.id','=','po.purchaseorder_detail_id');
            })->leftJoin('purchaseplan as pl',function($join){
                $join->on('po.purchaseplan_id','=','pl.id');
            })
            ->select($fields)
            ->whereNotIn('pd.state',[11,12,13])
            ->where('pd.quantity','>',DB::raw('pd.ware_quantity'))
            ->where(DB::raw('pd.quantity-pd.ware_quantity'),'>',0)
            ->orderBy('p.create_time','DESC')
            ->filter($request, CancelpoFilter::class);
    }

    /**
     * 撤销明细
     * @return mixed
     */
    public function buildDetaillResult(){
        return CancelPoBtDetail::query()->select([
                'sku',
                'po_id as po',
                'create_time',
                'state',
                'quantity',
                'orderer',
                'ware_quantity',
                'un_quantity',
                'bt_no',
                'cancel_num_total',
                'cancel_num_po',
                'rest_cancel_num',
                'cancel_num',
                'cancel_status',
                'bt_create_time',
                'is_show'
            ])
            ->orderBy('create_time','DESC')
            ->filter(request()->input('data', []), CancelpoFilter::class);
    }

    /**
     * 撤销总量
     */
    public function buildTotalResult(){
        return CancelPoBt::query()->from('cancel_po_bt','pd')->select([
            'bt_no',
            'sku',
            'pr_num',
            'un_quantity',
            'avl_sku_qty',
            'mod_aft_v3_qty',
            'base_num',
            'cancel_base_num',
            'count_cancel_num',
            'count_cancel_days',
            'su_cancel_num',
            'bt_create_time',
        ])
            ->orderBy('bt_create_time','DESC')
            ->filter(request()->input('data', []), CancelpoFilter::class);
    }
}
