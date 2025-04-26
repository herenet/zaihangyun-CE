# 更新用户手机号

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

更新当前登录用户的手机号码

该接口需要在APP后台启用登录

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/user/mobile` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| mcode | string | 格式：+xx | 是 | 国际电话区号，如：+86 |
| mobile | string | 手机号码格式 | 是 | 手机号码，中国大陆为11位数字 |
| verify_code | string | 最大6位字符 | 是 | 短信验证码 |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/user/mobile' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer MTc0NTY1Mzk1NQ==.fea11f8171c7576d1853321bc3164e40.RDVmY2VBMXNWdG1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'mcode=+86' \
--data-urlencode 'mobile=18518768888' \
--data-urlencode 'verify_code=347823'
```

- application/json方式：

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/user/mobile' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer MTc0NTIxOTg4OQ==.1c7f037215a4bde32073b5abf1c8a6c4.RDVmY2VBMXNWdG1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--data-raw '{
  "mcode": "+86",
  "mobile": "18518768888",
  "verify_code": "347823"
}'
```

> {warning} json方式请一定要在header中声明：`Content-Type: application/json`

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
    "code": "400107",
    "msg": "mobile must be valid"
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400104` | mcode参数缺失 |
| `400105` | mcode格式不正确 |
| `400106` | mobile参数缺失 |
| `400107` | mobile格式不正确 |
| `400108` | verify_code参数缺失 |
| `400109` | verify_code参数类型必须是字符串 |
| `400110` | verify_code长度不能超过6个字符 |
| `400190` | 验证码错误 |
| `400191` | 验证码已过期 |