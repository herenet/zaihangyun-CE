# 反馈列表查询

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取所有用户提交的反馈信息列表，支持分页查询和字段筛选。

### 注意事项

1. 需要用户登录后才能使用此接口
2. 需要确保消息功能在后台已开启
3. 返回的反馈列表按创建时间倒序排列
4. 最大支持每页100条记录
5. 可以通过参数控制是否返回回复内容和联系方式

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/message/feedback/list` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | - | 是 | 用户登录凭证，格式：Bearer {token} |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| page | integer | 否 | ≥1 | 1 | 页码 |
| page_size | integer | 否 | 1-100 | 10 | 每页记录数 |
| need_reply | integer | 否 | 0,1 | 0 | 是否返回回复内容：0-不返回，1-返回 |
| need_contact | integer | 否 | 0,1 | 0 | 是否返回联系方式：0-不返回，1-返回 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/message/feedback_list?page=1&page_size=10&need_reply=1&need_contact=0' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（need_reply=1, need_contact=0）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "page": 1,
        "page_size": 10,
        "total": 25,
        "list": [
            {
                "id": "fb_001",
                "content": "希望能增加深色模式功能",
                "type": 1,
                "reply": "感谢您的建议，我们正在开发中，预计下个版本上线",
                "updated_at": "2023-05-20 15:30:45",
                "created_at": "2023-05-15 10:20:30"
            },
            {
                "id": "fb_002",
                "content": "登录页面偶尔会出现闪退问题",
                "type": 2,
                "reply": null,
                "updated_at": "2023-05-18 09:15:20",
                "created_at": "2023-05-18 09:15:20"
            }
        ]
    }
}
```

### 返回参数说明

| 参数名 | 类型 | 说明 |
| -- | -- | -- |
| page | integer | 当前页码 |
| page_size | integer | 每页记录数 |
| total | integer | 总记录数 |
| list | array | 反馈列表数组 |
| list.id | string | 反馈ID |
| list.content | string | 反馈内容 |
| list.type | integer | 反馈类型：1-功能建议，2-问题反馈，99-其他 |
| list.reply | string/null | 回复内容，仅当need_reply=1时返回，未回复时为null |
| list.contact | string | 联系方式，仅当need_contact=1时返回 |
| list.updated_at | string | 更新时间 |
| list.created_at | string | 创建时间 |

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400199` | 消息接口未启用 |
| `400101` | 页码必须为整数 |
| `400102` | 页码必须大于0 |
| `400103` | 每页记录数必须为整数 |
| `400104` | 每页记录数必须大于0 |
| `400105` | 每页记录数不能超过100 |
| `400106` | need_reply参数必须为整数 |
| `400107` | need_reply参数值必须为0或1 |
| `400108` | need_contact参数必须为整数 |
| `400109` | need_contact参数值必须为0或1 |