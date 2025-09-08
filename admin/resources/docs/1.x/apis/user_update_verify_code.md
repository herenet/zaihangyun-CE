# 发送修改手机号短信验证码

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

更新用户手机时发送短信验证码

该接口需要在APP后台启用登录

### 注意事项

1. 验证码有效期为5分钟（可配置）
2. 手机号格式：
   - 中国大陆：11位数字，以1开头
   - 其他地区：请根据当地手机号格式规范
3. 国际区号格式：以+开头，后面跟1-3位数字，如：+86
4. 同一手机号每分钟只能发送一次验证码

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/user/verify_code` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| mcode | string | 格式为：+xx | 是 | 国际电话区号，如：+86 |
| mobile | string | 手机号码格式 | 是 | 手机号码，中国大陆为11位数字 |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/user/verify_code' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer ZaihangyunToken' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'mcode=+86' \
--data-urlencode 'mobile=18518768888'
```

- application/json方式：

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/user/verify_code' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer ZaihangyunToken' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--data-raw '{
  "mcode": "+86",
  "mobile": "18518768888",
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
| `400192` | 发送短信异常 |
| `400193` | 发送短信失败 |
| `400194` | 阿里云AccessKey配置未找到 |
| `400195` | 阿里云短信模板未找到 |
| `400196` | 阿里云短信签名未找到 |
| `400197` | 阿里云配置未找到 |
| `400198` | 登录接口配置未找到 |