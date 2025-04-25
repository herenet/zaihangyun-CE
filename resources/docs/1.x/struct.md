# 数据结构

---
- [用户模块](#section-1)
- [订单模块](#section-2)
- [文档模块](#section-3)

<a name="section-1"></a>
##用户数据结构

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