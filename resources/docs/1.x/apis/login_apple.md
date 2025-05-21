# 苹果登录（登录&注册）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

通过苹果账号（Apple ID）进行用户登录认证。

### 注意事项

1. 需要确保登录功能在后台已开启
2. 需要确保苹果登录方式在后台已开启
3. 首次登录会自动创建用户账号
4. 支持获取详细的用户信息

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/login/apple` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 在行云为应用分配的appkey |
| timestamp | string | 10位数字 | 是 | 当前时间戳（秒级） |
| sign | string | 32位小写字母和数字 | 是 | 签名，算法为：md5(appkey + timestamp + app_secret)，app_secret为在行云为应用分配的appSecret |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| user | string | 是 | 最大256字符 | - | 苹果用户标识 |
| token | string | 是 | 最大2048字符 | - | 苹果登录返回的身份令牌 |
| full_name | string | 否 | 最大64字符 | - | 用户全名（首次登录时使用） |
| version_number | integer | 是 | - | - | 应用版本号 |
| channel | string | 是 | 最大32字符 | - | 渠道标识 |
| need_user_detail | integer | 否 | 0,1 | 0 | 是否返回用户详细信息：0-不返回，1-返回 |
| oaid | string | 否 | 最大128字符 | - | 设备标识（安卓） |
| device_id | string | 否 | 最大128字符 | - | 设备ID |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/login/apple' \
--header 'Content-Type: application/json' \
--data-raw '{
    "appkey": "D5fceA1sVtmaMY1F",
    "timestamp": 1650381362,
    "sign": "5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8",
    "user": "001223.a19c841818a741e6b79c854a795b8f2d.0918",
    "token": "eyJraWQiOiJZdXlYb1kiLCJhbGciOiJSUzI1NiJ9...",
    "full_name": "张三",
    "version_number": 120,
    "channel": "appstore",
    "need_user_detail": 1,
    "device_id": "F7C1A1D5-D4D0-4E21-B35A-594F4FBCB904"
}'
```

> {warning} json方式请一定要在header中声明：`Content-Type: application/json`

<a name="section-5"></a>
## 返回响应

- 带用户信息返回（[查看字段解析](/{{route}}/{{version}}/struct#section-1)）。

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "uid": 1857248324,
        "wechat_openid": "1232dsfsadfasdfadsfdasfasdf",
        "wechat_unionid": "asdfsadfdasfdasfdasfdasfasdf",
        "apple_userid": "sdafasdfasdfdsafasf",
        "oaid": "4123123132",
        "device_id": "13213213123123",
        "username": "sdfsadfsd",
        "nickname": "herenet",
        "mcode": "+86",
        "mobile": "18518768888",
        "password": "e10adc3949ba59abbe56e057f20f883e",
        "email": "zaihangyun@126.com",
        "avatar": "https://www.zaihangyun.com/storage/mch/avatar/D5fceA1sVtmaMY1F/1857248324/03d62725bc9143307f9115092160a8cb.jpg",
        "gender": "1",
        "birthday": null,
        "country": "",
        "province": "",
        "city": "",
        "reg_ip": "127.0.0.1",
        "is_forever_vip": 1,
        "vip_expired_at": "2025-06-21 22:38:17",
        "enter_pass": "111111",
        "version_number": 1,
        "channel": "official",
        "reg_from": 99,
        "ext_data": "{    \"device_id\": \"7290832492f7bccf\",    \"c_number\": \"huawei\",    \"packagename\": \"com.ape.apefather\",    \"d_id\": \"16181\"}",
        "created_at": "2025-03-28 16:53:35",
        "access_token": "MTc0NTY1MzQ2NA==.6ffdd866ee53e1914268xxx9c6c7ad57.RDVmY2VBMXNWdG1hxxkxRi4yLjE4NTcyNDgzMjQ=",
        "token_expired_at": 1746517464
    }
}
```

- 不带用户信息

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "uid": 1857248324,
        "access_token": "MTc0NTY1Mzk1NQ==.fea11f8171c7xxxd1853321bc3164e40.RDVmY2VBMXNxxG1hTVkxRi4yLjE4NTcyNDgzMjQ=",
        "token_expired_at": 1746517955
    }
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400199` | appkey不存在 |
| `400198` | 登录接口配置不存在 |
| `400197` | 登录接口未启用 |
| `400196` | 苹果登录未开启 |
| `400195` | 苹果用户验证失败 |
| `400194` | 苹果登录异常，详见错误信息 |
| `400193` | 生成访问令牌失败 |
| `400104` | user参数缺失 |
| `400105` | user参数必须为字符串 |
| `400106` | user参数长度不能超过256个字符 |
| `400107` | full_name参数必须为字符串 |
| `400108` | full_name参数长度不能超过64个字符 |
| `400109` | token参数缺失 |
| `400110` | token参数必须为字符串 |
| `400111` | token参数长度不能超过2048个字符 |
| `400112` | version_number参数缺失 |
| `400113` | version_number参数必须为整数 |
| `400114` | channel参数缺失 |
| `400115` | channel参数长度不能超过32个字符 |
| `400116` | need_user_detail参数必须为整数 |
| `400117` | need_user_detail参数值必须为0或1 |
| `400118` | oaid参数必须为字符串 |
| `400119` | oaid参数长度不能超过128个字符 |
| `400120` | device_id参数必须为字符串 |
| `400121` | device_id参数长度不能超过128个字符 |
| `400250` | 创建用户失败 |