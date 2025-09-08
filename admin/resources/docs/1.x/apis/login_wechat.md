# 微信登录（登录&注册）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [业务流程图](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

为APP提供微信登录功能，能过该接口获取调用微信参数；必需在调用此接口前获取到微信登录的code。

该接口为免注册接口，登录成功同时系统会**同时完成注册**流程。（[查看流程图](#section-6)）

该接口需要在APP后台启用登录接口并配置好微信登录相关配置

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/login/wechat` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| code | string | 最大长度128字符 | 是 | 微信授权登录code |
| version_number | integer | 整数值 | 是 | 应用版本号 |
| channel | string | 最大长度32字符 | 是 | 渠道来源 |
| oaid | string | 最大长度128字符 | 否 | 设备唯一标识符(Android) |
| device_id | string | 最大长度128字符 | 否 | 设备ID |
| need_user_detail | integer | 0或1 | 否 | 是否需要返回用户详细信息，0-不需要，1-需要 |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/login/wechat' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'appkey=D5fceA1sVtmaMY1x' \
--data-urlencode 'code=12312312312' \
--data-urlencode 'version_number=12' \
--data-urlencode 'channel=vivo' \
--data-urlencode 'need_user_detail=0' \
--data-urlencode 'oaid=test' \
--data-urlencode 'device_id=test' \
--data-urlencode 'timestamp=1745649979' \
--data-urlencode 'sign=e6d8c298f4f2b0eb1eaf60afc3653532'
```

- application/json方式：

```javascript
curl --location --request POST 'http://127.0.0.1:8787/v1/login/wechat' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Accept: */*' \
--header 'Host: 127.0.0.1:8787' \
--header 'Connection: keep-alive' \
--data-raw '{
    "appkey" : "D5fceA1sVtmaMY1x",
    "code" : "xxxx",
    "version_number" : 1,
    "channel" : "vivo",
    "oaid" : "oaidtest",
    "need_user_detail" : 1,
    "device_id" : "test",
    "timestamp" : "1745652994",
    "sign" : "79b4f3825a6e033da1b262bdff8147ce"
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
## 业务流程图

<image src="/images/docs/wechat_login.png" width="1300px"/>


<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400104` | code参数缺失 |
| `400105` | code参数类型必须是字符串 |
| `400106` | code参数长度不能超过128个字符 |
| `400107` | version_number参数缺失 |
| `400108` | version_number参数类型必须是整数 |
| `400109` | channel参数缺失 |
| `400110` | channel参数长度不能超过32个字符 |
| `400111` | need_user_detail参数类型必须是整数 |
| `400112` | need_user_detail参数值必须是0或1 |
| `400113` | oaid参数类型必须是字符串 |
| `400114` | oaid参数长度不能超过128个字符 |
| `400115` | device_id参数类型必须是字符串 |
| `400116` | device_id参数长度不能超过128个字符 |
| `400193` | 生成访问令牌失败 |
| `400194` | 微信接口调用异常，返回详细错误信息 |
| `400195` | 微信开放平台配置未找到 |
| `400196` | 微信登录未开启 |
| `400197` | 登录接口未启用 |
| `400198` | 登录接口配置未找到 |
| `400199` | appkey不存在 |
| `400250` | 创建用户失败 |