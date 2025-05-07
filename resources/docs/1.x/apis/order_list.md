# 我的订单列表

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取当前登录用户的订单列表，支持按支付渠道和订单状态筛选。**不支持分页**

该接口需要在APP后台启用订单

### 注意事项

1. 价格单位为分
2. 返回的订单列表按创建时间降序排序
3. 不传pay_channel参数默认返回所有支付渠道的订单
4. 不传status参数默认返回已支付成功的订单
5. 不传limit参数默认每页最多返回10条数据
6. 最多返回100条数据

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/order/list` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| status | integer | 否 | 1:待支付, 2:已支付, 3:已退款, 4:支付失败 | 2 | 订单状态 |
| pay_channel | integer | 否 | 1:微信支付, 2:支付宝, 3:苹果支付 | - | 支付渠道 |
| limit | integer | 否 | 1-100 | 10 | 每页数量 |
| need_product_info | integer | 否 | 0,1 | 0 | 是否需要返回商品信息 |

<a name="section-4"></a>
## 请求示例代码

## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/order/my?status=2&pay_channel=1&limit=10' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiIxMjM0NTY3OCIsImFwcF9rZXkiOiJENWZjZUExc1Z0bWFNWTFGIiwiZXhwIjoxNjUwMzgxMzYyfQ.5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-2)）

```json
{
    "code": 200,
    "msg": "success",
    "data": [
        {
            "oid": "aabc-1aabc2025050421492698442542",
            "uid": 1857248324,
            "product_id": 267370,
            "product_price": 1900,
            "discount_amount": 0,
            "order_amount": 1900,
            "payment_amount": 1900,
            "platform_order_amount": null,
            "status": 1,
            "pay_channel": 1,
            "tid": null,
            "trade_type": null,
            "bank_type": null,
            "open_id": null,
            "channel": "official",
            "pay_time": null,
            "updated_at": null,
            "created_at": "2025-05-04 13:49:27",
            "product_info": {               //need_product_info为0,则无此字段
                "pid": 267370,
                "name": "永久会员修改",
                "sub_name": "月会员",
                "type": 1,
                "function_value": "30",
                "cross_price": 9900,
                "sale_price": 1900,
                "desc": null,
                "sale_status": 1,
                "platform_type": 1,
                "ext_data": "{\"test\":\"test\"}"
            }
        },
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
| `400102` | status参数值必须是1、2、3或4 |
| `400103` | pay_channel参数类型必须是整数 |
| `400104` | pay_channel参数值必须是1、2或3 |
| `400105` | limit参数类型必须是整数 |
| `400106` | limit参数值必须大于0 |
| `400107` | limit参数值必须小于100 |
| `400108` | need_product_info参数类型必须是整数 |
| `400109` | need_product_info参数值必须是0或1 |