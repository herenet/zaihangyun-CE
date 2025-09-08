# 获取应用配置

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取APP的特定配置信息。用于客户端动态获取APP相关配置参数。

### 注意事项

1. 不同的name对应不同类型的配置信息
2. 可适当将配置信息缓存到本地，注意控制配置接口的请求频率

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/app/config` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| name | string | 是 | - | - | 配置名称 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/app/config?appkey=D5fceA1sVtmaMY1F&timestamp=1650381362&sign=5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8&name=app_settings' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: application/json' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（自定义字段）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "app_version_str": "test",
        "app_version_number": "1",
        "domain": "www.baidu.com",
        "upgrade_text": "test",
        "last_version_number": "1",
        "mast_upgrade_number": "1",
        "upgrade_from": "1",
        "comment_rate": "90",
        "good_rate": "90",
        ...
    }
}
```

- 错误返回

```json
{
    "code": 400101,
    "msg": "name is required"
}
```

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | name参数缺失 |
| `400199` | 配置不存在 |