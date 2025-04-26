# 获取登录短信验证码

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

该接口为使用手机验证码登录时获取登录短信验证码时使用

该接口需要在APP后台启用登录接口并配置好短信登录相关配置

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/login/verify_code` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| mcode | string | 格式为：+xx | 是 | 国际电话区号，如：+86 |
| mobile | string | 手机号码格式 | 是 | 手机号码，中国大陆为11位数字 |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/login/verify_code' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'appkey=D5fceA1sVtmaMY1F' \
--data-urlencode 'mcode=+86' \
--data-urlencode 'timestamp=1745653704' \
--data-urlencode 'sign=89baeb05b60b59108087960b077963ca' \
--data-urlencode 'mobile=18518768888'
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
  "timestamp": "1745653954",
  "sign": "42704343af5fbc4a6639eb4826a53e91"
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
| `400196` | 手机登录未开启 |
| `400197` | 登录接口未启用 |
| `400198` | 登录接口配置未找到 |
| `400199` | appkey不存在 |