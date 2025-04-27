# 数据结构

---
- [用户模块](#section-1)
- [订单模块](#section-2)
- [文档模块](#section-3)

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

###订单数据结构

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

### 产品数据结构

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
| platform_type | unsigned int | 适用平台（1=所有平台，2=安卓，3=苹果），默认1 | 1 |
| order | unsigned int | 排序权重，值越小越靠前，默认1 | 1 |
| updated_at | datetime | 更新时间 | 2024-04-15 15:30:25 |
| created_at | datetime | 创建时间 | 2024-04-15 15:20:10 |

<a name="section-3"></a>
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