<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/10
 * Time: 12:09 下午
 */

namespace App\Services\MrpBaseData;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use App\Models\MrpBaseData\MrpBasePlatformList;
use App\Tools\Formater;
use App\Tools\Motan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountService
{
    // 销售人员角色ID
    private const SALE_ROLE_ID = [
        '10' => '销售总监',
        '11' => '销售经理',
        '12' => '销售主管',
        '13' => '销售专员',
    ];

    // 客服人员角色ID
    private const CUSTOMER_ROLE_ID = [
        '20' => '客服总监',
        '21' => '客服经理',
        '22' => '客服主管',
        '23' => '客服专员',
    ];

    /**
     * 同步OMS账号资料数据
     * @author dwj
     */
    public static function syncBaseAccountListData()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步销售账号数据 || 开始");
        $i = 0;
        $data = MrpBaseOmsSalesList::query()
            ->select([
                'sales_account as account',
                'platform_code',
            ])
            ->groupBy('sales_account', 'platform_code')
            ->get()
            ->toArray();

        $data = array_chunk($data, 3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            $platformCodes = array_unique(array_column($items, 'platform_code'));
            $accounts = array_column($items, 'account');
            $platformNames = self::getPlatformNameByCode($platformCodes);
            $accountInfos = self::getAccountInfoByAccounts($accounts);
            foreach ($items as $item) {
                $account = $item['account'] ?? '';
                $platformCode = $item['platform_code'] ?? '';
                if (empty($account) || empty($platformCode)) { // 排除脏数据
                    continue;
                }
                $temp[] = [
                    'account'         => $account,
                    'platform_code'   => $platformCode,
                    'platform_name'   => $platformNames[$platformCode] ?? '',
                    'site'            => $accountInfos[$account]['site'] ?? '',
                    'manager_cn_name' => $accountInfos[$account]['manager_cn_name'] ?? '',
                    'manager_account' => $accountInfos[$account]['manager_account'] ?? '',
                    'department'      => $accountInfos[$account]['business_type'] ?? '',
                    'zg_account'      => $accountInfos[$account]['zg_account'] ?? '',
                    'jl_account'      => $accountInfos[$account]['jl_account'] ?? '',
                    'zj_account'      => $accountInfos[$account]['zj_account'] ?? '',
                    'kf_account'      => $accountInfos[$account]['kf_account'] ?? '',
                ];
            }
            $sql = Formater::sqlInsertAll('mrp_base_accounts_lists', $temp, [
                'account',
                'platform_code',
                'platform_name',
                'site',
                'manager_cn_name',
                'manager_account',
                'department',
                'zg_account',
                'jl_account',
                'zj_account',
                'kf_account'
            ]);
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 同步销售账号数据花费时间：{$timeDiff}分,执行完{$i}条");
    }

    /**
     * 获取平台名称
     * @param  $platformCodes  : 平台编码
     * @return array
     * @author dwj
     */
    public static function getPlatformNameByCode($platformCodes)
    {
        return MrpBasePlatformList::query()
            ->whereIn('platform_code', $platformCodes)
            ->select([
                'platform_code',
                'platform_cn_name',
            ])
            ->pluck('platform_cn_name', 'platform_code')
            ->toArray();
    }


    /**
     * 获取账号的站点、销售、客服角色人员
     * @param  array  $accounts  : 账号
     * @return array
     * @throws InvalidRequestException
     * @throws InternalException
     * @author dwj
     */
    public static function getAccountInfoByAccounts(array $accounts)
    {
        $url = config('host.iactmgr_rpc.'.config('app.env')); // 新账号系统URL
        $param = ['accountNames' => $accounts];
        $curlRes = Motan::call('getAccountInfo', $param, $url);
        $result = [];
        $list = $curlRes['list'] ?? [];
        if (empty($list)) {
            throw new InvalidRequestException('Motan接口返回数据有误！');
        }
        foreach ($list as $v) {
            $accountName = $v['accountName'];
            $platformCode = $v['platformCode'];
            $site = $v['site'];
            $firstDepartmentName = $v['firstDepartmentName'];
            $tmp = [
                'account'         => $accountName,
                'platform'        => $platformCode,
                'site'            => $site,
                'business_type'   => $firstDepartmentName,
                'manager'         => '',
                'manager_account' => '',
            ];
            $managerArr = [];
            $kfArr = [];
            foreach ($v['roleUserInfoList'] as $v1) {
                //roleId 10:销售总监 11:销售经理；12:销售主管；13:销售专员
                if (in_array($v1['roleId'], array_keys(self::SALE_ROLE_ID))) {
                    $managerArr[$v1['roleId']] = [
                        'manager_cn_name' => join(";", array_column($v1['userInfoList'], 'userNameCn')),
                        'manager_account' => join(";", array_column($v1['userInfoList'], 'userName')),
                    ];
                }
                //roleId 20:客服总监 21:客服经理 22:客服主管 23:客服专员
                if (in_array($v1['roleId'], array_keys(self::CUSTOMER_ROLE_ID))) {
                    $kfArr[$v1['roleId']] = [
                        'kf_cn_name' => $v1['userInfoList'][0]['userNameCn'],
                        'kf_account' => $v1['userInfoList'][0]['userName'],
                    ];
                }
            }
            $tmp['zg_account'] = $managerArr[12]['manager_account'] ?? ''; // 主管
            $tmp['jl_account'] = $managerArr[11]['manager_account'] ?? ''; // 经理
            $tmp['zj_account'] = $managerArr[10]['manager_account'] ?? ''; // 总监
            krsort($managerArr);
            krsort($kfArr);
            $managerArr = array_values($managerArr);
            $kfArr = array_values($kfArr);
            $result[$accountName] = array_merge($tmp, $managerArr[0] ?? [], $kfArr[0] ?? []);
        }
        return $result;
    }
}
