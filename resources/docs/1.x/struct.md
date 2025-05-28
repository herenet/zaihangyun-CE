# 数据结构

---
- [用户模块](#section-1)
- [订单模块](#section-2)
- [文档模块](#section-4)

<a name="section-1"></a>
##用户模块

### 用户数据结构

| 字段名           | 类型       | 说明                     | 示例值 |
|------------------|------------|--------------------------|--------|
| uid              | unsigned int    | 用户唯一ID               | 1857248324 |
| wechat_openid    | string     | 微信 OpenID              | 1232dsfsadfasdfadsfdasfasdf |
| wechat_unionid   | string     | 微信 UnionID             | asdfsadfdasfdasfdasfdasfasdf |
| apple_userid     | string     | Apple 用户ID             | sdafasdfasdfdsafasf |
| oaid             | string     | 设备的 OAID              | 4123123132 |
| device_id        | string     | 设备ID（旧）             | 13213213123123 |
| username         | string     | 用户名                   | sdfsadfsd |
| nickname         | string     | 昵称                     | herenet |
| mcode            | string     | 国家区号代码（默认：+86）                 | +86 |
| mobile           | string     | 手机号                   | 18518763128 |
| password         | string     | 密码签名   | e10adc3949ba59abbe56e057f20fxx3e |
| email            | string     | 电子邮箱                 | herenet@126.com |
| avatar           | string     | 头像URL                  | https://www.zaihangyun.com/storage/mch/avatar/D5fY1F/185324/03d620a8cb.jpg |
| gender           | unsigned int | 性别（0=未知，1=男，2=女），默认0       | 1 |
| birthday         | date | 生日（格式：YYYY-MM-DD）| 2011-11-11 |
| country          | string     | 国家                     | 中国 |
| province         | string     | 省份                     | 湖南 |
| city             | string     | 城市                     | 长沙 |
| reg_ip           | string     | 注册IP地址               | 127.0.0.1 |
| is_forever_vip   | unsigned int    | 是否永久VIP（0=否，1=是）默认0      | 1 |
| vip_expired_at   | datetime   | VIP到期时间              | 2025-06-21 22:38:17 |
| enter_pass       | string     | 应用启动密码    | 111111 |
| version_number   | unsigned int   | 用户数据版本号           | 1 |
| channel          | string     | 渠道标识（默认：official）                 | official |
| reg_from         | unsigned int    | 注册来源（1=手机号，2=微信，3=苹果，99=后台）         | 99 |
| ext_data         | JSON       | 自定义扩展数据（JSON字符串）   | { "device_id": "7290832492f7bccf", "c_number": "huawei", ... } |
| created_at       | datetime   | 创建时间（注册时间）     | 2025-03-28 16:53:35 |


<a name="section-2"></a>
##订单模块

###订单数据结构（Android）

| 字段名 | 类型 | 说明 | 示例值 |
|--------|------|------|--------|
| oid | string | 订单唯一ID | 202404151234567890 |
| uid | unsigned int | 用户ID | 1857248324 |
| product_id | unsigned int | 产品ID | 1001 |
| product_price | unsigned int | 产品价格（单位：分） | 9900 |
| discount_amount | unsigned int | 优惠金额（单位：分），默认0 | 500 |
| order_amount | unsigned int | 订单金额（单位：分） | 9400 |
| payment_amount | unsigned int | 实际支付金额（单位：分） | 9400 |
| platform_order_amount | unsigned int | 支付平台订单金额（单位：分） | 9400 |
| status | unsigned int | 订单状态（1=待支付，2=已支付，3=已退款，4=支付失败），默认1 | 2 |
| pay_channel | unsigned int | 支付渠道（1=微信，2=支付宝，3=苹果），默认1 | 1 |
| tid | string | 第三方支付平台订单号 | 4200001234202404151234567890 |
| trade_type | string | 交易类型 | APP |
| bank_type | string | 银行类型 | CMC |
| open_id | string | 第三方支付用户标识 | oFX_p5GBJXt3U9lk_xxxxxxxx |
| channel | string | 来源渠道，默认official | official |
| pay_time | datetime | 支付时间 | 2024-04-15 15:30:25 |
| updated_at | datetime | 更新时间 | 2024-04-15 15:30:25 |
| created_at | datetime | 创建时间 | 2024-04-15 15:20:10 |


<a name="section-6"></a>
###订单数据结构（Apple）

| 字段名 | 类型 | 说明 | 示例值 |
|--------|------|------|--------|
| oid | string | 订单唯一ID | AP202404151234567890123456 |
| uid | unsigned int | 用户ID | 1857248324 |
| product_id | unsigned int | 产品ID | 1001 |
| apple_product_id | string | Apple产品标识符 | com.example.premium_monthly |
| product_type | unsigned int | 产品类型（0=消耗型，1=非消耗型，2=自动续费订阅，3=非自动续费订阅） | 2 |
| amount | unsigned int | 订单金额（单位：分），`非实际支付金额，仅作参考` | 9400 |
| payment_status | unsigned int | 支付状态（1=待支付，2=已支付，3=已退款，4=支付失败），默认1 | 2 |
| subscription_status | unsigned int | 订阅状态（1=活跃，2=已过期，3=已取消，4=宽限期，5=暂停），仅订阅产品 | 1 |
| transaction_id | string | Apple交易ID | 2000000123456789 |
| original_transaction_id | string | 原始交易ID | 2000000123456789 |
| purchase_date | datetime | 购买时间 | 2024-04-15 15:30:25 |
| original_purchase_date | datetime | 原始购买时间 | 2024-04-15 15:30:25 |
| expires_date | datetime | 过期时间（仅订阅产品） | 2024-05-15 15:30:25 |
| cancellation_date | datetime | 取消时间 | 2024-04-20 10:15:30 |
| is_trial_period | unsigned int | 是否试用期（0=否，1=是） | 0 |
| is_in_intro_offer_period | unsigned int | 是否介绍性优惠期（0=否，1=是） | 0 |
| auto_renew_status | unsigned int | 自动续费状态（0=关闭，1=开启） | 1 |
| auto_renew_product_id | string | 自动续费产品ID | com.example.premium_monthly |
| environment | string | 环境类型（Sandbox=沙盒，Production=生产） | Production |
| data_source | unsigned int | 数据来源（1=Receipt验证，2=S2S通知） | 1 |
| updated_at | datetime | 更新时间 | 2024-04-15 15:30:25 |
| created_at | datetime | 创建时间 | 2024-04-15 15:20:10 |

#### 字段说明
* 订阅相关字段：
1. subscription_status、expires_date、auto_renew_status 等字段仅对订阅类型产品有效
2. 非订阅产品这些字段值为 NULL 或默认值
* Apple特有字段：
1. transaction_id：Apple提供的唯一交易标识
2. original_transaction_id：用于关联续费订单的原始交易ID
3. apple_product_id：在App Store Connect中配置的产品标识符
4. environment：区分沙盒测试和生产环境
* 数据来源：
1. data_source=1：通过Receipt验证创建/更新的订单
2. data_source=2：通过Apple S2S通知创建/更新的订单


<a name="section-3"></a>
### 产品数据结构（Android）

| 字段名 | 类型 | 说明 | 示例值 |
|--------|------|------|--------|
| pid | unsigned int | 产品唯一ID | 1001 |
| name | string | 产品名称 | 高级会员月卡 |
| sub_name | string | 子标题 | 尊享所有高级功能30天 |
| type | unsigned int | 产品类型（1=会员时长，2=永久会员，99=自定义），默认1 | 1 |
| function_value | string | 产品功能值，根据产品类型适配购买后逻辑处理 | 30 |
| cross_price | unsigned int | 划线价（单位：分） | 12900 |
| sale_price | unsigned int | 售价（单位：分） | 9900 |
| desc | string | 商品描述 | 解锁全部高级功能，畅享无限使用体验 |
| sale_status | unsigned int | 销售状态（1=在售，2=待售），默认1 | 1 |
| order | unsigned int | 排序权重，值越小越靠前，默认1 | 1 |
| ext_data |  JSON  | 自定义扩展数据（JSON字符串）   | { "test": "test", ... } |
| updated_at | datetime | 更新时间 | 2024-04-15 15:30:25 |
| created_at | datetime | 创建时间 | 2024-04-15 15:20:10 |


<a name="section-5"></a>
### 产品数据结构（Apple）

| 字段名 | 类型 | 说明 | 示例值 |
|--------|------|------|--------|
| pid | bigint unsigned | 产品唯一ID | 1001 |
| iap_product_id | varchar(128) | 苹果产品ID | com.example.app.monthly |
| name | varchar(32) | 产品名称 | 高级会员月卡 |
| sub_name | varchar(64) | 子标题 | 尊享所有高级功能30天 |
| is_subscription | tinyint unsigned | 是否为订阅（0=否，1=是），默认0 | 1 |
| subscription_duration | tinyint unsigned | 苹果订阅时长周期类型（1=周，2=月，3=双月，4=季度，5=半年，6=年） | 2 |
| type | tinyint unsigned | 产品类型（1=会员时长，2=永久会员，99=自定义），默认1 | 1 |
| function_value | varchar(64) | 产品功能值，用于购买后逻辑处理 | 30 |
| cross_price | int unsigned | 划线价，单位分 | 12900 |
| sale_price | int unsigned | 售价，单位分 | 9900 |
| desc | varchar(512) | 商品描述 | 解锁全部高级功能，畅享无限使用体验 |
| sale_status | tinyint unsigned | 销售状态（1=在售，2=待售），默认1 | 1 |
| order | int unsigned | 排序，值越小越靠前，默认1 | 1 |
| ext_data | varchar(512) | 用户自定义扩展字段，JSON格式 | {"tag": "hot"} |
| updated_at | timestamp | 更新时间 | 2024-04-15 15:30:25 |
| created_at | timestamp | 创建时间 | 2024-04-15 15:20:10 |


<a name="section-4"></a>
##文档模块

### 文章数据结构

| 字段名 | 类型 | 说明 | 示例值 |
|--------|------|------|--------|
| id | string | 文章唯一ID | a1b2c3d4e5f6 |
| title | string | 文章标题 | 用户使用指南 |
| type | unsigned int | 文章类型（默认1） | 1 |
| content | string | 文章内容 | 这是一篇详细介绍应用功能的使用指南... |
| order | unsigned int | 排序值，数值越小越靠前，默认1 | 10 |
| updated_at | datetime | 更新时间 | 2024-04-15 15:30:25 |
| created_at | datetime | 创建时间 | 2024-04-15 15:20:10 |