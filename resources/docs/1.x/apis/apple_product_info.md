# 获取Apple平台产品详情

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取指定Apple平台商品的详细信息。与普通商品详情接口的区别在于，此接口专门用于获取苹果应用内购买/订阅商品的信息。

### 注意事项

1. 需要提供正确的商品ID(pid)
2. 商品ID必须属于当前应用(appkey)
3. 此接口专门用于iOS平台的应用内购买/订阅商品

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/product/iap/info` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 应用唯一标识 |
| timestamp | integer | - | 是 | 请求时间戳（秒） |
| sign | string | - | 是 | 签名，详见[签名算法](/{{route}}/{{version}}/intro#section-3) |

### 业务参数
| 参数名 | 类型 | 是否必须 | 说明 |
| -- | -- | -- | -- |
| pid | string | 是 | 商品ID |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap/info' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "pid": 1001
}'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-5)）

```json
{
    "code": 200,
    "msg": "success",
     "data": {
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
    }
}
```

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | pid参数缺失 |
| `400102` | pid参数必须为字符串 |
| `400103` | pid参数长度超出限制 |