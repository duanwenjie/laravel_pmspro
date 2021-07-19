# 合同审批回调

描述:合同模块审核成功后调用

|方法|URI|请求头|
|:-|:-|:-|
|POST|`/Server/receiveContractInfo`|Default|


### URL 参数

```text
None
```

###  请求参数
|参数|类型|说明|是否必填|备注|
|:-|:-|:-|
|supplierId|integer|供应商ID|Y| |
|purchaseOrderId|string|采购单ID|N| 采购合同供货合同需要此字段|
|contractCode|string|合同编码|Y| |
|confirmNodeDesc|string|审核节点描述|Y| 如:产品审批 |
|confirmStatus|integer|审核状态|Y| 如:1:审核通过,2:审核驳回,3:审核中 |
|confirmUser|string|审核人|Y|如:李春  |
|contractReturnRate|string|合同关键条款-约定退款率|Y| |
|contractReturnRateRemark|string|合同关键条款-约定退款率-备注|Y| |
|contractDeliveryDate|string|合同关键条款-约定交期|Y| |
|contractDeliveryDateRemark|string|合同关键条款-约定交期-备注|Y| |
|contractDeliveryPlace|string|合同关键条款-约定交货地点|Y| |
|contractDeliveryPlaceRemark|string|合同关键条款-约定交货地点-备注|Y| |
|contractMarginLevel|string|合同关键条款-退款率保证金比例|Y| |
|contractMarginLevelRemark|string|合同关键条款-退款率保证金比例-备注|Y| |
|contractBreachLevel|string|合同关键条款-交期逾期违约金比例|Y| |
|contractBreachLevelRemark|string|合同关键条款-交期逾期违约金比例-备注|Y| |
|contractAccountPeriod|string|合同关键条款-账期|Y| |
|contractAccountPeriodRemark|string|合同关键条款-账期-备注|Y| |

> {primary} 参数示例

```json
{
     "supplierId":54425,
     "purchaseOrderId":"1865990102",
     "contractCode":"HT-1865990102",
     "confirmNodeDesc":"待产品审核",
     "confirmStatus":1,
     "confirmUser":"李春",
     "contractReturnRate":"1",
     "contractReturnRateRemark":"1",
     "contractDeliveryDate":"1",
     "contractDeliveryDateRemark":"1",
     "contractDeliveryPlace":"1",
     "contractDeliveryPlaceRemark":"1",
     "contractMarginLevel":"1",
     "contractMarginLevelRemark":"1",
     "contractBreachLevel":"1",
     "contractBreachLevelRemark":"1",
     "contractAccountPeriod":"1",
     "contractAccountPeriodRemark":"1"
}
```

> {success} 成功响应


```json
{
    "state": "000001",
    "msg": "处理成功",
    "data": []
}
```

> {danger} 失败响应


```json
{
    "state": "000400",
    "msg": "处理失败",
    "data": []
}
```

