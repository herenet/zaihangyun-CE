# 创建订单

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [返回字段解析](#section-6)
- [业务流程图](#section-7)
- [错误码说明](#section-8)

<a name="section-1"></a>
## 接口说明

创建商品订单，支持免费商品（产品价格为0）

该接口需要在APP后台启用订单

### 注意事项

1. 每个用户1秒最多创建1个订单
2. 商品价格为0时自动完成订单
3. 目前仅支持微信支付，其它支付暂未开放
4. 微信支付返回的参数用于APP调用支付SDK
5. 商品类型说明：
   - TYPE_MEMBER_DURATION(1): 时长会员，购买成功后延长会员时间
   - TYPE_MEMBER_FOREVER(2): 永久会员，购买成功后永久有效
   - TYPE_MEMBER_CUSTOM(99): 自定义会员，暂未开放

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/order/create` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 说明 |
| -- | -- | -- | -- | -- |
| pid | integer | 是 | - | 商品ID |
| pay_channel | integer | 是 | 1:微信支付, 2:支付宝, 3:苹果支付 | 支付渠道 |
| channel | string | 否 | 长度最大32字符 | 渠道来源，默认"official" |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/order/create' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer ZaihangyunToken' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'pid=267370' \
--data-urlencode 'pay_channel=1' \
--data-urlencode 'channel='
```

- application/json方式：

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/order/create' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer ZaihangyunToken' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--data-raw '{
  "pid": "267370",
  "pay_channel": "1",
  "channel": ""
}'
```

<a name="section-5"></a>
## 返回响应

- 免费商品成功响应

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "120230419201602123456"  // 订单号
    }
}
```

- 选择微信支付创建订单成功响应

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "12025050120355825756800",
        "appid": "wx123456789",        // 微信开放平台appid
        "partnerid": "1900000109",     // 微信支付商户号
        "prepay_id": "wx201410272009395522657a690389285100", // 预支付交易会话标识
        "package": "Sign=WXPay",       // 固定值
        "noncestr": "a1b2c3d4e5",      // 随机字符串
        "timestamp": 1666666666,       // 时间戳
        "sign": "abcdefghijklmn"       // 签名
    }
}
```

- 选择支付宝创建订单成功响应

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "22025050120355825756800",
        "order_str": "method=......5EhA%3D%3D"
    }
}
```

- 错误返回

```json
{
    "code": 400199,
    "msg": "product not found"
}
```


<a name="section-6"></a>
## 返回字段解析

### 免费商品 data 对象
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 订单号 |

### 微信支付 data 对象
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 订单号 |
| appid | string | 微信开放平台审核通过的应用APPID |
| partnerid | string | 微信支付分配的商户号 |
| prepay_id | string | 微信返回的支付交易会话ID |
| package | string | 固定值"Sign=WXPay" |
| noncestr | string | 随机字符串，长度为10位 |
| timestamp | integer | 时间戳，标准北京时间，时区为东八区，自1970年1月1日 0点0分0秒以来的秒数 |
| sign | string | 签名，使用字段：appid、timestamp、noncestr、prepay_id，详见微信支付API文档 |

### 支付宝 data 对象
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 订单号 |
| order_str | string | 支付宝签名字符串 |


<a name="section-7"></a>
## 业务流程图

###通过微信支付
<image src="/images/docs/wechat_pay.png" width="1300px"/>

###通过支付宝支付
<image src="/images/docs/alipay.png" width="1300px"/>

<a name="section-8"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400104` | pid参数缺失 |
| `400105` | pid参数类型必须是整数 |
| `400107` | pay_channel参数缺失 |
| `400108` | pay_channel参数类型必须是整数 |
| `400109` | pay_channel参数值不在指定范围内 |
| `400110` | channel参数类型必须是字符串 |
| `400111` | channel参数长度不能超过32个字符 |
| `400189` | 不支持的支付渠道 |
| `400190` | 微信支付接口检查未通过 |
| `400191` | 未找到微信开放平台配置 |
| `400192` | 未找到微信商户配置 |
| `400193` | 未设置微信平台配置 |
| `400194` | 未设置微信商户配置 |
| `400195` | 未开启微信支付 |
| `400196` | 未开启订单接口 |
| `400197` | 未找到订单接口配置 |
| `400198` | 商品已下架 |
| `400199` | 商品不存在 |
| `400250` | 创建订单失败 |