# 提交意见反馈

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

提交用户反馈信息，包括功能建议、问题反馈等类型的内容。

### 注意事项

1. 需要用户登录后才能使用此接口
2. 需要确保消息功能在后台已开启
3. 反馈类型包括：功能建议(1)、问题反馈(2)、其他(99)

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/message/feedback` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | - | 是 | 用户登录凭证，格式：Bearer {token} |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| content | string | 是 | 最大255字符 | - | 反馈内容 |
| type | integer | 是 | 1,2,99 | - | 反馈类型：1-功能建议，2-问题反馈，99-其他 |
| contact | string | 否 | 最大64字符 | - | 联系方式，如邮箱、手机号等 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/message/feedback' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c' \
--header 'Content-Type: application/json' \
--data-raw '{
    "content": "希望能增加深色模式功能",
    "type": 1,
    "contact": "example@mail.com"
}'
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
    "code": 400101,
    "msg": "message interface is not enabled"
}
```

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | 消息接口未启用 |
| `400104` | 反馈内容不能为空 |
| `400105` | 反馈内容必须为字符串 |
| `400106` | 反馈内容不能超过255个字符 |
| `400107` | 反馈类型不能为空 |
| `400108` | 反馈类型必须为整数 |
| `400109` | 反馈类型必须是1、2或99之一 |
| `400110` | 联系方式不能为空 |
| `400111` | 联系方式必须为字符串 |
| `400112` | 联系方式不能超过64个字符 |
| `400250` | 创建反馈失败 |