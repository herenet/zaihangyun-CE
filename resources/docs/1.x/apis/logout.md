# 退出登录

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

退出当前用户的登录状态，使当前token失效

该接口需要在APP后台启用登录

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/user/logout` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

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
curl --location --request GET 'https://api.zaihangyun.com/v1/user/logout' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer MTc0NTY1Mzk1NQ==.fea11f8171c7576d1853xxxbc3164e40.RDVmY2VBMXNWxx1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回

```json
{
    "code": 200,
    "msg": "success"
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
| - | - |