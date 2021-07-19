# 概览


- [访问地址](#访问地址)
- [鉴权](#鉴权)
- [状态码](#状态码)
- [返回格式](#返回格式)

<a name="访问地址"></a>
- 测试环境 `http://test.pmspro.kokoerp.com/api/`
- 正式环境 `http://pmspro.kokoerp.com/api/`



<a name="鉴权"></a>
## 鉴权  access_token 找李春获取
- Content-Type ： application/json
- Authorization：Bearer access_token



<a name="状态码"></a>
## 状态码


| 状态码|描述|
|:----------:|:-------------:|
| 000001 | 成功 |
| 000400 | 业务请求错误，具体错误看描述|
| 000401 | 鉴权失败 (权限系统token已过期或者token无效) |
| 000405 | 请求方式不允许|
| 000403 | 无权限访问|
| 000422 | 请求数据异常 |
| 000500 | 系统错误 |

<a name="返回格式"></a>
## 返回格式

| 字段   |      类型      |  描述 |
|----------|:-------------:|------:|
| status | string |  |
| msg|    string   |   返回消息 |
| data |  object或者数组 |    业务数据 |


