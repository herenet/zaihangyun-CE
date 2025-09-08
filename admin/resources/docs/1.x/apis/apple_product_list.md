# 获取产品列表（Apple）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [产品类型说明](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

获取苹果应用内购买(IAP)商品列表，支持按销售状态、商品类型和Apple产品类型筛选。

### 注意事项

1. 商品按照排序值（order）升序排列
2. 默认返回最多100条记录
3. 默认返回上架状态的商品
4. 支持会员时长、永久会员等多种商品类型
5. 价格单位为分
6. **支持Apple产品类型筛选**：消耗型、非消耗型、自动续期订阅、非续期订阅
7. **function_value字段说明**：
   - 当type=1(时长会员)时，表示会员时长天数
   - 当type=2(永久会员)时，固定值为"forever_vip"

### 使用场景

- **消费型商品**：游戏币、道具、VIP时长等可重复购买的商品
- **非消费型商品**：去广告、解锁功能等一次性购买的商品
- **自动续期订阅**：月度会员、年度会员等自动续费的订阅服务
- **非续期订阅**：季度通行证等固定期限的订阅服务

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/product/iap/list` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| status | integer | 否 | 1,2 | 1 | 销售状态：1-上架，2-下架 |
| type | integer | 否 | 1,2,99 | - | 功能模型：1-时长会员，2-永久会员，99-自定义会员 |
| apple_product_type | integer | 否 | 1,2,3,4 | - | Apple产品类型：1-消耗型，2-非消耗型，3-自动续期订阅，4-非续期订阅 |

<a name="section-4"></a>
## 请求示例代码

### 获取所有上架商品
```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap/list' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "status": 1
}'
```

### 获取自动续期订阅商品
```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap/list' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "status": 1,
    "apple_product_type": 3
}'
```

### 获取永久会员商品
```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/product/iap/list' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "status": 1,
    "type": 2
}'
```

<a name="section-5"></a>
## 返回响应

### 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-5)）

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
            "apple_product_type": 2,
            "subscription_duration": null,
            "type": 2,
            "function_value": "forever_vip",
            "cross_price": 199900,
            "sale_price": 9900,
            "desc": "苹果的商店就是麻烦",
            "sale_status": 1,
            "ext_data": "{}"
        },
        {
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
            "desc": "自动续费月度会员",
            "sale_status": 1,
            "ext_data": "{}"
        }
    ]
}
```

### 返回字段说明

| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| pid | integer | 商品ID |
| iap_product_id | string | Apple产品标识符，用于IAP购买 |
| name | string | 商品名称 |
| sub_name | string | 商品子标题 |
| apple_product_type | integer | Apple产品类型：1-消耗型，2-非消耗型，3-自动续期订阅，4-非续期订阅 |
| subscription_duration | integer | 订阅时长类型：1-1周，2-1个月，3-2个月，4-3个月，5-6个月，6-1年 |
| type | integer | 功能模型：1-时长会员，2-永久会员，99-自定义会员 |
| function_value | string | 功能值，用于业务逻辑处理 |
| cross_price | integer | 划线价格（单位：分） |
| sale_price | integer | 销售价格（单位：分） |
| desc | string | 商品描述 |
| sale_status | integer | 销售状态：1-上架，2-下架 |
| ext_data | string | 扩展数据（JSON格式） |

### 错误返回

```json
{
    "code": 400105,
    "msg": "apple_product_type must be integer"
}
```

<a name="section-6"></a>
## 产品类型说明

### Apple产品类型 (apple_product_type)

| 类型值 | 类型名称 | 说明 | 购买特性 |
| -- | -- | -- | -- |
| 1 | 消耗型 | 游戏币、道具等 | 可重复购买，需要被消耗 |
| 2 | 非消耗型 | 去广告、解锁功能等 | 一次购买，永久拥有 |
| 3 | 自动续期订阅 | 月度/年度会员等 | 自动续费，可取消 |
| 4 | 非续期订阅 | 季度通行证等 | 固定期限，不自动续费 |

### 功能模型 (type)

| 类型值 | 类型名称 | 说明 | function_value示例 |
| -- | -- | -- | -- |
| 1 | 时长会员 | 按天数计算的会员 | "30"（30天） |
| 2 | 永久会员 | 永久有效的会员 | "forever_vip" |

### 订阅时长类型 (subscription_duration)

| 类型值 | 时长说明 | 天数 |
| -- | -- | -- |
| 1 | 1周 | 7天 |
| 2 | 1个月 | 30天 |
| 3 | 2个月 | 60天 |
| 4 | 3个月 | 90天 |
| 5 | 6个月 | 180天 |
| 6 | 1年 | 365天 |

### 产品类型组合建议

| 业务场景 | apple_product_type | type | subscription_duration |
| -- | -- | -- | -- |
| 会员时长 | 1（消耗型） | 1（时长会员） | 多次购买，会员时长累计 |
| 去广告功能 | 2（非消耗型） | 2（永久会员） | null |
| 月度会员 | 3（自动续期订阅） | 1（时长会员） | 2（1个月） |
| 年度会员 | 3（自动续期订阅） | 1（时长会员） | 6（1年） |
| 季度通行证 | 4（非续期订阅） | 1（时长会员） | 4（3个月） |

<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

### 参数验证错误
| 错误码 | 说明 |
| -- | -- |
| `400101` | status参数必须为整数 |
| `400102` | status参数值必须为1或2 |
| `400103` | type参数必须为整数 |
| `400104` | type参数值必须为1、2或99 |
| `400105` | apple_product_type参数必须为整数 |
| `400106` | apple_product_type参数值必须为1、2、3或4 |

### 错误处理建议

1. **参数类型错误（400101, 400103, 400105）**：
   - 检查参数数据类型是否正确
   - 确保传递的是整数而非字符串

2. **参数值范围错误（400102, 400104, 400106）**：
   - 检查参数值是否在允许的范围内
   - 参考产品类型说明表确认正确的参数值

3. **最佳实践**：
   - 优先使用 `apple_product_type` 筛选，更符合Apple IAP的分类逻辑
   - 结合 `type` 和 `apple_product_type` 可以实现精确筛选
   - 建议按业务场景选择合适的筛选条件