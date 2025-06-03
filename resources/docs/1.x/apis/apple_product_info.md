# 获取产品详情（Apple）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [产品字段说明](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

获取指定Apple平台商品的详细信息。此接口专门用于获取苹果应用内购买(IAP)商品的完整信息，包括产品类型、价格、订阅配置等。

### 注意事项

1. 需要提供正确的商品ID(pid)
2. 商品ID必须属于当前应用(appkey)
3. 此接口专门用于iOS平台的应用内购买商品
4. 返回的商品信息包含Apple产品类型、订阅时长等IAP专用字段
5. 价格单位为分

### 使用场景

- **商品详情展示**：在购买页面展示商品的详细信息
- **订单创建前**：获取商品信息进行价格和类型验证
- **购买页面**：展示商品名称、描述、价格等信息
- **订阅管理**：获取订阅商品的时长和类型信息

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

### 获取永久会员商品详情
```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap/info' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "pid": "55513647"
}'
```

### 获取订阅商品详情
```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap/info' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "pid": "55513648"
}'
```

<a name="section-5"></a>
## 返回响应

### 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-5)）

#### 永久会员商品示例
```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "pid": 55513647,
        "iap_product_id": "timestamp.kongmuhu.com.forver_vip",
        "name": "永久会员",
        "sub_name": "永久",
        "apple_product_type": 2,
        "subscription_duration": null,
        "type": 2,
        "function_value": "forever_vip",
        "cross_price": 199900,
        "sale_price": 9900,
        "desc": "享受永久会员特权，一次购买终身有效",
        "sale_status": 1,
        "ext_data": "{\"features\":[\"去广告\",\"高级功能\",\"优先支持\"]}"
    }
}
```

#### 月度订阅商品示例
```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "pid": 55513648,
        "iap_product_id": "timestamp.kongmuhu.com.monthly_vip",
        "name": "月度会员",
        "sub_name": "1个月",
        "apple_product_type": 3,
        "subscription_duration": 2,
        "type": 1,
        "function_value": "30",
        "cross_price": 3900,
        "sale_price": 1900,
        "desc": "自动续费月度会员，可随时取消",
        "sale_status": 1,
        "ext_data": "{\"auto_renew\":true,\"trial_period\":\"7天免费试用\"}"
    }
}
```

#### 消耗型商品示例
```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "pid": 55513649,
        "iap_product_id": "timestamp.kongmuhu.com.coins_100",
        "name": "金币包",
        "sub_name": "100金币",
        "apple_product_type": 1,
        "subscription_duration": null,
        "type": 99,
        "function_value": "100",
        "cross_price": 0,
        "sale_price": 600,
        "desc": "购买100个游戏金币",
        "sale_status": 1,
        "ext_data": "{\"coins\":100,\"bonus\":10}"
    }
}
```

### 商品不存在错误
```json
{
    "code": 200,
    "msg": "success",
    "data": []
}
```

### 参数错误返回
```json
{
    "code": 400101,
    "msg": "pid is required"
}
```

<a name="section-6"></a>
## 产品字段说明

### 基本信息字段
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| pid | integer | 商品ID |
| iap_product_id | string | Apple产品标识符，用于IAP购买 |
| name | string | 商品名称 |
| sub_name | string | 商品子标题 |
| desc | string | 商品描述 |
| sale_status | integer | 销售状态：1-上架，2-下架 |

### Apple相关字段
| 字段名 | 类型 | 说明 | 取值范围 |
| -- | -- | -- | -- |
| apple_product_type | integer | Apple产品类型 | 1-消耗型，2-非消耗型，3-自动续期订阅，4-非续期订阅 |
| subscription_duration | integer/null | 订阅时长类型 | 1-1周，2-1个月，3-2个月，4-3个月，5-6个月，6-1年 |

### 业务逻辑字段
| 字段名 | 类型 | 说明 | 取值范围 |
| -- | -- | -- | -- |
| type | integer | 功能模型，用于业务逻辑处理 | 1-时长会员，2-永久会员 |
| function_value | string | 功能值，用于业务逻辑处理 | 时长会员：天数；永久会员："forever_vip"；自定义：自定义值 |

### 价格字段
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| cross_price | integer | 划线价格（单位：分），0表示无划线价 |
| sale_price | integer | 销售价格（单位：分） |

### 扩展字段
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| ext_data | string | 扩展数据（JSON格式），可存储商品的额外配置信息 |

### 字段组合说明

#### 非消耗型商品 (apple_product_type=2)
- `subscription_duration`: null（非消耗型商品无订阅概念）
- `type`: 通常为2（永久会员）
- `function_value`: "forever_vip"或自定义值

#### 自动续期订阅 (apple_product_type=3)
- `subscription_duration`: 必须设置具体的时长类型
- `type`: 通常为1（时长会员）
- `function_value`: 对应的天数值

#### 非续期订阅 (apple_product_type=4)
- `subscription_duration`: 可设置时长类型
- `type`: 通常为1（时长会员）
- `function_value`: 对应的天数值

<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

### 参数验证错误
| 错误码 | 说明 |
| -- | -- |
| `400101` | pid参数缺失 |
| `400102` | pid参数必须为字符串 |

### 特殊说明

1. **商品不存在**：当商品不存在时，接口返回成功状态但data为空数组`[]`
2. **商品权限**：只能查询属于当前appkey的商品
3. **数据安全**：返回的数据已过滤敏感字段（如app_key、tenant_id等）

### 错误处理建议

1. **参数错误（400101-400102）**：
   - 检查pid参数是否正确传递
   - 确认pid参数类型为字符串

2. **商品不存在（data为空）**：
   - 检查商品ID是否正确
   - 确认商品是否属于当前应用
   - 检查商品是否已被删除

3. **最佳实践**：
   - 调用前先通过产品列表接口获取有效的pid
   - 根据返回的apple_product_type判断商品类型并做相应处理
   - 利用ext_data字段存储和获取商品的扩展配置信息
   - 注意价格字段单位为分，展示时需要转换为元