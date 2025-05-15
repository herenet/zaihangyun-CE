# 获取文档分类列表

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

获取文章分类列表，支持分页。使用该接口前需要确保文章功能已在后台开启。

### 注意事项

1. 返回的分类按照创建时间倒序排列
2. 最大支持每页100条记录

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/article/category/list` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 应用唯一标识 |
| timestamp | integer | - | 是 | 请求时间戳（秒） |
| sign | string | - | 是 | 签名，详见[签名算法](/{{route}}/{{version}}/intro#section-3) |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 默认值 | 说明 |
| -- | -- | -- | -- | -- | -- |
| page | integer | 否 | ≥1 | 1 | 页码 |
| page_size | integer | 否 | 1-100 | 10 | 每页记录数 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/article/category/list?appkey=D5fceA1sVxxxMY1F&timestamp=1650381362&sign=5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8&page=1&page_size=10' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
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
    "msg": "success",
    "data": {
        "page": 1,
        "page_size": 10,
        "total": 25,
        "list": [
            {
                "id": "cat_001",
                "name": "技术文章",
                "desc": "关于技术方面的文章分类",
                "updated_at": "2023-05-20 12:34:56",
                "created_at": "2023-05-15 08:30:00"
            },
            {
                "id": "cat_002",
                "name": "产品更新",
                "desc": "产品更新与改进说明",
                "updated_at": "2023-05-18 15:20:30",
                "created_at": "2023-05-10 09:15:00"
            }
        ]
    }
}
```

- 错误返回

```json
{
    "code": 400101,
    "msg": "page must be greater than 0"
}
```

### 返回参数说明

| 参数名 | 类型 | 说明 |
| -- | -- | -- |
| page | integer | 当前页码 |
| page_size | integer | 每页记录数 |
| total | integer | 总记录数 |
| list | array | 分类列表数组 |
| list.id | string | 分类ID |
| list.name | string | 分类名称 |
| list.desc | string | 分类描述 |
| list.updated_at | string | 更新时间 |
| list.created_at | string | 创建时间 |

<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | 页码必须大于0 |
| `400102` | 每页记录数必须大于0 |
| `400103` | 每页记录数不能超过100 |
| `400104` | 文章配置不存在 |
| `400105` | 文章接口未启用 |