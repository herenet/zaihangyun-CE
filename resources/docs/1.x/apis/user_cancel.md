# 用户账户注销

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

注销当前登录用户的账号，用户申请注销成功后会**同时退出当前登录态**，同时系统会根据接口配置执行**延迟删除**操作

该接口需要在APP后台启用登录

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/user/cancel` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

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

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/user/cancel' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer MTc0NTIxOTg4OQ==.1c7f037215a4bde32073b5abf1c8a6c4.RDVmY2VBMXNWdG1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
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
    "msg": "success"
}
```

- 错误返回

```json
{
    "code": 400250,
    "msg": "cancel user failed"
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400250` | 注销用户失败 |