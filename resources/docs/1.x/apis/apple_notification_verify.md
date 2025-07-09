# 支付票据验证（Apple）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [完整票据数据说明](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

验证Apple应用内购买（IAP）的支付凭证，该接口是轻量级的独立验证服务，不依赖于系统的订单体系。

### 主要功能

1. **独立验证服务**：无需依赖订单系统，可直接验证Apple收据
2. **环境自适应**：支持沙盒和生产环境，自动处理环境切换（21007错误时）
3. **精确交易定位**：通过transaction_id精确验证指定交易（解决混合购买场景）
4. **重复验证控制**：可配置是否允许重复验证同一收据
5. **安全验证**：Bundle ID匹配验证，防止恶意验证
6. **完整数据保存**：验证成功后保存Apple完整原始票据数据

### 注意事项

1. 该接口无需用户先登录
2. **transaction_id为必填参数**：用于精确定位要验证的交易，解决用户多种购买类型共存的问题
3. 支持重复验证控制，可配置是否允许验证相同收据
4. 自动处理生产/沙盒环境切换（21007错误时）
5. 验证结果会被保存，包含完整的Apple原始响应数据，可通过后台查看

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/apple/receipt/verify` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 在行云为应用分配的appkey |
| timestamp | string | 10位数字 | 是 | 当前时间戳（秒级） |
| sign | string | 32位小写字母和数字 | 是 | 签名，算法为：md5(appkey + timestamp + app_secret)，app_secret为在行云为应用分配的appSecret |

### 业务参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | 最大64字符 | 是 | 应用标识 |
| receipt_data | string | - | 是 | Apple收据数据（Base64编码） |
| environment | string | Sandbox,Production | 是 | 验证环境 |
| transaction_id | string | 最大128字符 | **是** | **要验证的具体交易ID** |

### transaction_id 参数详解

`transaction_id` 参数用于精确指定要验证的交易，**此参数为必填**：

**为什么必填：**
- 解决混合购买场景：用户可能既有订阅商品又有非订阅商品
- 精确验证：确保验证的是用户指定的具体交易
- 避免歧义：防止验证到错误的交易信息

**参数来源：**
- iOS端：从 `SKPaymentTransaction.transactionIdentifier` 获取
- 订阅续费：从Apple S2S通知中获取
- 历史交易：从之前的验证结果中获取

**系统行为：**
- 系统会在收据的所有交易中（`latest_receipt_info` 和 `in_app`）查找指定的 `transaction_id`
- 如果找不到指定的交易ID，返回错误信息
- 找到后返回该交易的详细信息

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/apple/receipt/verify' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'appkey=D5fceA1sVtmaMY1x' \
--data-urlencode 'receipt_data=ewoJInNpZ25hdHVyZSIgPSAi...' \
--data-urlencode 'environment=Production' \
--data-urlencode 'transaction_id=2000000933865029' \
--data-urlencode 'timestamp=1745649979' \
--data-urlencode 'sign=e6d8c298f4f2b0eb1eaf60afc3653532'
```

- application/json方式：

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/apple/receipt/verify' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--data-raw '{
    "appkey": "D5fceA1sVtmaMY1x",
    "receipt_data": "ewoJInNpZ25hdHVyZSIgPSAi...",
    "environment": "Production",
    "transaction_id": "2000000933865029",
    "timestamp": "1745652994",
    "sign": "79b4f3825a6e033da1b262bdff8147ce"
}'
```

<a name="section-5"></a>
## 返回响应

- 验证成功响应

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "verification_id": 2017247833,
        "status": "success",
        "bundle_id": "com.kongmuhu.timestamp",
        "environment": "Sandbox",
        "transaction_id": "2000000933865029",
        "original_transaction_id": "2000000933865029",
        "product_id": "timestamp.kongmuhu.com.monthly_test",
        "purchase_date": "2025-06-05 11:10:09",
        "quantity": 1
    }
}
```

- 验证失败响应

```json
{
    "code": 400306,
    "msg": "receipt verification failed",
    "data": {
        "verification_id": 3482881541,
        "status": "failed",
        "apple_status_code": 21002,
        "error_message": "The data in the receipt-data property was malformed or missing."
    }
}
```

- 重复验证错误响应

```json
{
    "code": 400306,
    "msg": "receipt already verified, duplicate verification not allowed"
}
```

- 交易ID未找到响应

```json
{
    "code": 400399,
    "msg": "Transaction ID '2000000933865029' not found in receipt"
}
```

<a name="section-6"></a>
## 完整票据数据说明

接口返回的是解析后的关键字段，完整的Apple原始票据数据已保存在系统中，可通过后台查看。

### 返回字段说明

#### 成功响应字段
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| verification_id | integer | 验证记录ID，用于查询完整票据数据 |
| status | string | 验证状态：success |
| bundle_id | string | 应用Bundle ID |
| environment | string | 验证环境（Sandbox/Production） |
| transaction_id | string | 交易ID（与请求参数中的transaction_id对应） |
| original_transaction_id | string | 原始交易ID |
| product_id | string | 苹果产品ID |
| purchase_date | string | 购买时间 |
| quantity | integer | 购买数量 |
| expires_date | string | 过期时间（订阅产品，可选） |
| is_trial_period | integer | 是否试用期：0=否，1=是（订阅产品，可选） |
| cancellation_date | string | 取消时间（已取消的订阅，可选） |

#### 失败响应字段
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| verification_id | integer | 验证记录ID |
| status | string | 验证状态：failed |
| apple_status_code | integer | Apple返回的状态码 |
| error_message | string | 错误信息 |

### 完整票据数据结构

系统保存的完整票据数据包含Apple的完整原始响应，主要包含以下结构：

#### receipt 对象
- `receipt_type`: 收据类型
- `adam_id`: App Store应用ID
- `app_item_id`: 应用项目ID
- `bundle_id`: 应用Bundle ID
- `application_version`: 应用版本
- `download_id`: 下载ID
- `version_external_identifier`: 版本外部标识符
- `receipt_creation_date`: 收据创建时间
- `receipt_creation_date_ms`: 收据创建时间（毫秒）
- `receipt_creation_date_pst`: 收据创建时间（PST）
- `request_date`: 请求时间
- `request_date_ms`: 请求时间（毫秒）
- `request_date_pst`: 请求时间（PST）
- `original_purchase_date`: 原始购买时间
- `original_purchase_date_ms`: 原始购买时间（毫秒）
- `original_purchase_date_pst`: 原始购买时间（PST）
- `original_application_version`: 原始应用版本
- `in_app`: 应用内购买交易数组


#### latest_receipt_info 数组

包含所有交易信息，按时间降序排列，每个交易包含：
- `quantity`: 购买数量
- `product_id`: 产品ID
- `transaction_id`: 交易ID
- `original_transaction_id`: 原始交易ID
- `purchase_date`: 购买时间
- `purchase_date_ms`: 购买时间（毫秒）
- `purchase_date_pst`: 购买时间（PST）
- `original_purchase_date`: 原始购买时间
- `original_purchase_date_ms`: 原始购买时间（毫秒）
- `original_purchase_date_pst`: 原始购买时间（PST）
- `expires_date`: 过期时间（订阅产品）
- `expires_date_ms`: 过期时间（毫秒）
- `expires_date_pst`: 过期时间（PST）
- `web_order_line_item_id`: 网页订单行项目ID
- `is_trial_period`: 是否试用期
- `is_in_intro_offer_period`: 是否促销期
- `subscription_group_identifier`: 订阅组标识符
- `promotional_offer_id`: 促销优惠ID
- `in_app_ownership_type`: 应用内购买所有权类型


#### pending_renewal_info 数组

包含待续费信息：
- `auto_renew_product_id`: 自动续费产品ID
- `original_transaction_id`: 原始交易ID
- `product_id`: 产品ID
- `auto_renew_status`: 自动续费状态
- `expiration_intent`: 过期原因
- `grace_period_expires_date`: 宽限期过期时间
- `grace_period_expires_date_ms`: 宽限期过期时间（毫秒）
- `grace_period_expires_date_pst`: 宽限期过期时间（PST）
- `is_in_billing_retry_period`: 是否在计费重试期
- `offer_code_ref_name`: 优惠码引用名称
- `price_consent_status`: 价格同意状态
- `promotional_offer_id`: 促销优惠ID

<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

### 参数验证错误

| 错误码 | 说明 |
| -- | -- |
| `400101` | appkey参数必填 |
| `400102` | appkey参数长度不能超过64个字符 |
| `400103` | receipt_data参数必填 |
| `400104` | environment参数必填 |
| `400105` | environment参数必须是Sandbox或Production |
| `400106` | **transaction_id参数必填** |
| `400107` | transaction_id参数必须是字符串类型 |
| `400108` | transaction_id参数长度不能超过128个字符 |

### 配置相关错误

| 错误码 | 说明 |
| -- | -- |
| `400300` | 订单接口配置未找到 |
| `400301` | 订单接口已关闭 |
| `400302` | Apple验证功能已关闭 |
| `400303` | Apple验证配置缺失 |
| `400304` | Bundle ID未配置 |
| `400305` | 共享密钥未配置 |
| `400306` | 收据已验证且不允许重复验证 |
| `400307` | Bundle ID不匹配 |

### 系统错误

| 错误码 | 说明 |
| -- | -- |
| `400399` | 收据验证失败（包括指定的transaction_id在收据中未找到） |