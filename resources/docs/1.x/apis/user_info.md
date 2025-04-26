# 获取用户详情

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取当前登录用户的详细信息

该接口需要在APP后台启用登录

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/user/info` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| - | - | - | - | - |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/user/info' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer MTc0NTY1Mzk1NQ==.fea11f8171c7576d1853xxxbc3164e40.RDVmY2VBMXNWxx1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-1)）

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
        "birthday": "1987-03-04",
        "country": "",
        "province": "",
        "city": "",
        "reg_ip": "127.0.0.1",
        "is_forever_vip": 1,
        "vip_expired_at": "2025-03-28 00:00:00",
        "enter_pass": "111111",
        "version_number": 1,
        "channel": "official",
        "reg_from": 99,
        "ext_data": "{    \"device_id\": \"7290832492f7bccf\",    \"c_number\": \"huawei\",    \"packagename\": \"com.ape.apefather\",    \"d_id\": \"16181\"}",
        "created_at": "2025-03-28 16:53:35"
    }
}
```

- 错误返回

```json
{
    "code": 401,
    "msg": "Unauthorized"
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400199` | 用户不存在 |