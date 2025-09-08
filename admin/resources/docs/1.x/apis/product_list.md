# 获取产品列表（Android）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取产品列表，支持按状态、平台和类型筛选。**不支持分页**

该接口需要在APP后台启用订单

苹果平台产品请移步（[获取Apple平台产品列表](/{{route}}/{{version}}/apis/apple_product_list)）

### 注意事项

1. 价格单位为分
2. 返回的产品列表按order字段升序排序
3. 不传status参数默认返回在售商品
4. 不传type参数默认返回所有类型商品
5. function_value字段：
   - 当type=1(时长会员)时，表示会员时长天数
   - 当type=2(永久会员)时，固定值为"forever_vip"
6. 最多返回100条数据

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/product/list` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 在行云为应用分配的appkey |
| timestamp | string | 10位数字 | 是 | 当前时间戳（秒级） |
| sign | string | 32位小写字母和数字 | 是 | 签名，算法为：md5(appkey + timestamp + app_secret)，app_secret为在行云为应用分配的appSecret |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| status | integer | 否 | 1:在售, 2:下架 | 1 | 商品状态 |
| type | integer | 否 | 1:时长会员, 2:永久会员, 99:自定义会员 | 1 | 商品类型 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/list?status=1&platform=1&type=&timestamp=1745747633&sign=683ba956baf26efacca393f341aad943&appkey=D5fceAxxxtmaMY1F' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-3)）

```json
{
    "code": 200,
    "msg": "success",
    "data": [
        {
            "pid": 267370,             // 产品ID
            "name": "永久会员修改",      // 产品名称
            "sub_name": "月会员",       // 产品副标题
            "type": 1,                 // 商品类型
            "function_value": "30",    // 功能值
            "cross_price": 9900,       // 划线价格(分)
            "sale_price": 1900,        // 销售价格(分)
            "desc": null,              // 商品描述
            "sale_status": 1,          // 销售状态
            "ext_data": "{\"test\":\"test\"}"   //用户自定义扩展数据
        }
        ...
    ]
}
```

- 错误返回

```json
{
    "code": 400101,
    "msg": "status must be integer"
}
```

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | status参数类型必须是整数 |
| `400102` | status参数值必须是1或2 |
| `400103` | platform参数类型必须是整数 |
| `400104` | platform参数值必须是1、2或3 |
| `400105` | type参数类型必须是整数 |
| `400106` | type参数值必须是1、2或99 |