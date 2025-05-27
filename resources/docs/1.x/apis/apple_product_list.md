# 获取Apple平台产品列表

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取苹果应用内购买(IAP)/订阅商品列表，支持按状态、商品类型和是否为订阅筛选。

### 注意事项

1. 商品按照排序值（order）升序排列
2. 默认返回最多100条记录
3. 默认返回上架状态的商品
4. 支持会员时长、永久会员等多种商品类型
5. 价格单位为分
6. function_value字段：
   - 当type=1(时长会员)时，表示会员时长天数
   - 当type=2(永久会员)时，固定值为"forever_vip"

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/product/iap_list` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 应用唯一标识 |
| timestamp | integer | - | 是 | 请求时间戳（秒） |
| sign | string | - | 是 | 签名，详见[签名算法](/{{route}}/{{version}}/intro#section-3) |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| status | integer | 否 | 1,2 | 1 | 商品状态：1-上架，2-下架 |
| type | integer | 否 | 1,2,99 | - | 商品类型：1-时长会员，2-永久会员，99-自定义会员 |
| is_subscription | integer | 否 | 0,1 | - | 是否订阅商品：0-否，1-是 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap_list' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "status": 1,
    "type": 1,
    "is_subscription": 1
}'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-5)）

```json
{
    "code": 200,
    "msg": "success",
    "data": [
        {
            "pid": 55513647,
            "iap_product_id": "timestamp.kongmuhu.com.forver_vip",
            "name": "永久会员",
            "sub_name": "永久",
            "is_subscription": 0,
            "subscription_duration": null,
            "type": 2,
            "function_value": "forever_vip",
            "cross_price": 199900,
            "sale_price": 9900,
            "desc": "苹果的商店就是麻烦",
            "sale_status": 1,
            "ext_data": "{}"
        },
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
| `400101` | status参数必须为整数 |
| `400102` | status参数值必须为1或2 |
| `400103` | type参数必须为整数 |
| `400104` | type参数值必须为1、2或99 |
| `400105` | is_subscription参数必须为整数 |
| `400106` | is_subscription参数值必须为0或1 |