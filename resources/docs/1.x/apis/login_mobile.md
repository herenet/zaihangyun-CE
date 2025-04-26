# 手机验证码登录（登录&注册）

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

为APP提供手机验证码登录功能，在调用此接口前需先调用[获取登录短信验证码](/{{route}}/{{version}}/apis/login_verify_code)接口获取短信验证码。

该接口为免注册接口，登录成功同时系统会**同时完成注册**流程。（[查看流程图](#section-6)）

该接口需要在APP后台启用登录接口并配置好短信登录相关配置

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/login/mobile` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| mcode | string | 格式为：+xx，默认+86 | 是 | 国际电话区号，如：+86 |
| mobile | string | 手机号码格式 | 是 | 手机号码，中国大陆为11位数字 |
| verify_code | string | 最大长度6位数字 | 是 | 短信验证码 |
| version_number | integer | 整数值 | 是 | 应用版本号 |
| channel | string | 最大长度32字符 | 是 | 渠道来源 |
| oaid | string | 最大长度128字符 | 否 | 设备唯一标识符(Android) |
| device_id | string | 最大长度128字符 | 否 | 设备ID |
| need_user_detail | integer | 0或1 | 否 | 是否需要返回用户详细信息，0-不需要，1-需要 |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/login/mobile' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'appkey=D5fceA1sVtmaMY1F' \
--data-urlencode 'mcode=+86' \
--data-urlencode 'mobile=18518768888' \
--data-urlencode 'verify_code=507149' \
--data-urlencode 'version_number=12' \
--data-urlencode 'channel=vivo' \
--data-urlencode 'need_user_detail=0' \
--data-urlencode 'timestamp=1745653954' \
--data-urlencode 'sign=42704343af5fbc4a6639eb4826a53e91'
```

- application/json方式：

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/login/mobile' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--data-raw '{
  "appkey": "D5fceA1sVtmaMY1F",
  "mcode": "+86",
  "mobile": "18518768888",
  "verify_code": "507149",
  "version_number": "12",
  "channel": "vivo",
  "need_user_detail": "0",
  "timestamp": "1745653954",
  "sign": "42704343af5fbc4a6639eb4826a53e91"
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

<image src="/images/docs/mobile_login.png" width="1300px"/>


<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| 错误码 | 说明 |
| -- | -- |
| `400104` | mcode参数缺失 |
| `400105` | mcode格式不正确 |
| `400106` | mobile参数缺失 |
| `400107` | mobile格式不正确 |
| `400108` | verify_code参数缺失 |
| `400109` | verify_code参数类型必须是字符串 |
| `400110` | verify_code长度不能超过6个字符 |
| `400111` | version_number参数缺失 |
| `400112` | version_number参数类型必须是整数 |
| `400113` | channel参数缺失 |
| `400114` | channel长度不能超过32个字符/oaid长度不能超过128个字符 |
| `400115` | need_user_detail参数类型必须是整数/device_id参数类型必须是字符串 |
| `400116` | need_user_detail参数值必须是0或1/device_id长度不能超过128个字符 |
| `400117` | oaid参数类型必须是字符串 |
| `400193` | 生成访问令牌失败 |
| `400194` | 验证码不正确 |
| `400195` | 验证码未找到或已过期 |
| `400196` | 手机登录未开启 |
| `400197` | 登录接口未启用 |
| `400198` | 登录接口配置未找到 |
| `400199` | appkey不存在 |
| `400250` | 创建用户失败 |