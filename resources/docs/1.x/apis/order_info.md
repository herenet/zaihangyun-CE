# 获取订单详情

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取指定订单号的订单详细信息

该接口需要在APP后台启用登录

### 注意事项

1. 只能查询当前登录用户自己的订单
2. 价格单位为分
3. 需要提供完整的订单号

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/order/info` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 是否必须 | 说明 |
| -- | -- | -- | -- |
| oid | string | 是 | 订单号 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/order/info?oid=aabc-12025050421492698442542' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer ZaihangyunToken' \
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
    "data": {
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
        "created_at": "2025-05-04 13:49:27"
    }
}
```

- 错误返回

```json
{
    "code": 401,
    "msg": "Unauthorized"
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | 订单号(oid)参数缺失 |
| `400102` | 订单不存在或不属于当前用户 |