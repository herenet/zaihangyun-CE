# 我的订单列表（Apple）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取当前登录用户的苹果订单列表，支持按支付状态、订阅状态筛选，可选择返回商品详情。

### 注意事项

1. 需要用户登录认证
2. 只能查询当前登录用户的订单
3. 订单按创建时间倒序排列
4. payment_status和subscription_status不传值或为null则返回所有订单

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/order/apple/list` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | - | 是 | 用户登录Token |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| payment_status | integer | 否 | 1,2,3,4 | - | 支付状态：1-待验证，2-支付成功，3-支付失败，4-已退款 |
| subscription_status | integer | 否 | 1,2,3,4,5 | - | 订阅状态：1-活跃，2-已过期，3-已取消，4-宽限期，5-计费重试 |
| limit | integer | 否 | 1-100 | 10 | 返回记录数量限制 |
| need_product_info | integer | 否 | 0,1 | 0 | 是否返回商品详情：0-否，1-是 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/order/apple/list' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "payment_status": 2,
    "subscription_status": 1,
    "limit": 20,
    "need_product_info": 1
}'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（不包含商品详情）（[查看字段解析](/{{route}}/{{version}}/struct#section-6)）

```json
{
    "code": 200,
    "msg": "success",
    "data": [
        {
            "oid": "AO202404151530001",
            "uid": "10001",
            "product_id": "1001",
            "apple_product_id": "com.example.app.monthly",
            "product_type": 3,
            "amount": 9900,
            "payment_status": 2,
            "subscription_status": 1,
            "transaction_id": "1000000123456789",
            "original_transaction_id": "1000000123456789",
            "environment": "production",
            "is_trial_period": 0,
            "is_in_intro_offer_period": 0,
            "expires_date": "2024-05-15 15:30:25",
            "grace_period_expires_date": null,
            "auto_renew_status": 1,
            "auto_renew_product_id": "com.example.app.monthly",
            "purchase_date": "2024-04-15 15:30:25",
            "original_purchase_date": "2024-04-15 15:30:25",
            "cancellation_date": null,
            "data_source": 1,
            "updated_at": "2024-04-15 15:30:25",
            "created_at": "2024-04-15 15:30:25"
        }
        ...
    ]
}
```

- 成功返回（包含商品详情）

```json
{
    "code": 200,
    "msg": "success",
    "data": [
        {
            "oid": "AO202404151530001",
            "uid": "10001",
            "product_id": "1001",
            "apple_product_id": "com.example.app.monthly",
            "product_type": 3,
            "amount": 9900,
            "payment_status": 2,
            "subscription_status": 1,
            "transaction_id": "1000000123456789",
            "original_transaction_id": "1000000123456789",
            "environment": "production",
            "is_trial_period": 0,
            "is_in_intro_offer_period": 0,
            "expires_date": "2024-05-15 15:30:25",
            "grace_period_expires_date": null,
            "auto_renew_status": 1,
            "auto_renew_product_id": "com.example.app.monthly",
            "purchase_date": "2024-04-15 15:30:25",
            "original_purchase_date": "2024-04-15 15:30:25",
            "cancellation_date": null,
            "data_source": 1,
            "updated_at": "2024-04-15 15:30:25",
            "created_at": "2024-04-15 15:30:25",
            "product_info": {
                "pid": "1001",
                "iap_product_id": "com.example.app.monthly",
                "name": "高级会员月卡",
                "sub_name": "尊享所有高级功能30天",
                "is_subscription": 1,
                "subscription_duration": 2,
                "type": 1,
                "function_value": "30",
                "cross_price": 12900,
                "sale_price": 9900,
                "desc": "解锁全部高级功能，畅享无限使用体验",
                "sale_status": 1,
                "ext_data": {
                    "tag": "hot"
                }
            }
        }
        ...
    ]
}
```

### 返回参数说明

| 参数名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 内部订单号 |
| uid | bigint | 用户ID |
| product_id | integer | 内部产品ID |
| apple_product_id | string | 苹果产品标识符 |
| product_type | integer | 产品类型：1-消耗型，2-非消耗型，3-自动续期订阅，4-非续期订阅 |
| amount | integer | 订单金额（单位：分） |
| payment_status | integer | 支付状态：1-待验证，2-支付成功，3-支付失败，4-已退款 |
| subscription_status | integer | 订阅状态：1-活跃，2-已过期，3-已取消，4-宽限期，5-计费重试 |
| transaction_id | string | 苹果交易ID |
| original_transaction_id | string | 原始交易ID（订阅关联标识） |
| environment | string | 环境：sandbox-沙盒，production-生产 |
| is_trial_period | integer | 是否试用期：0-否，1-是 |
| is_in_intro_offer_period | integer | 是否促销期：0-否，1-是 |
| expires_date | datetime | 订阅过期时间 |
| grace_period_expires_date | datetime | 宽限期过期时间 |
| auto_renew_status | integer | 自动续订状态：0-关闭，1-开启 |
| auto_renew_product_id | string | 下一周期续订的产品ID |
| purchase_date | datetime | 购买时间 |
| original_purchase_date | datetime | 原始购买时间 |
| cancellation_date | datetime | 取消时间（退款时苹果返回） |
| data_source | integer | 数据来源：1-Receipt验证，2-S2S通知 |
| updated_at | datetime | 更新时间 |
| created_at | datetime | 创建时间 |
| product_info | object | 商品详情（当need_product_info=1时返回） |

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400105` | limit参数必须为整数 |
| `400106` | limit参数必须大于0 |
| `400107` | limit参数必须小于等于100 |
| `400108` | need_product_info参数必须为整数 |
| `400109` | need_product_info参数值必须为0或1 |