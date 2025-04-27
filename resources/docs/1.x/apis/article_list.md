# 获取文档列表

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [返回字段解析](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

获取文章列表，支持分页查询

### 注意事项

1. 接口采用签名认证方式
2. 分页参数page和page_size如不传递，默认返回第1页，每页10条数据
3. 每篇文章都包含一个可**直接访问的URL地址**
4. total字段表示文章总数

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/article/list` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| page | integer | ≥1 | 否 | 页码，默认1 |
| page_size | integer | 1-100 | 否 | 每页条数，默认10 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/article/list?page=1&page_size=10&appkey=D5fceA1sVxxxMY1F&timestamp=1745745346&sign=1d127bda330f808d1641e65955c248ad' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
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
        "page": 1,
        "page_size": 10,
        "total": 100,
        "list": [
            {
                "id": "29555205829",         // 文章ID
                "title": "测试一下",          // 文章标题
                "order": 1,                  // 文章排序值
                "updated_at": "2025-04-19 20:16:02",  // 更新时间
                "created_at": "2025-04-19 20:16:02",  // 创建时间
                "link": "https://www.zaihangyun.com/article/D5fceA1xxtmaMY1F/29555205829"  // 文章详情页URL
            }
            // ... 更多文章
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

<a name="section-6"></a>
## 返回字段解析

### data 对象

| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| page | integer | 当前页码 |
| page_size | integer | 每页显示条数 |
| total | integer | 文章总数 |
| list | array | 文章列表数组 |

### list 数组元素

| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| id | string | 文章ID |
| title | string | 文章标题 |
| order | integer | 文章排序值 |
| updated_at | string | 更新时间，格式：YYYY-MM-DD HH:mm:ss |
| created_at | string | 创建时间，格式：YYYY-MM-DD HH:mm:ss |
| link | string | 文章详情页URL，格式：域名/article/appkey/文章ID |

<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | 页码必须大于0 |
| `400102` | 每页条数必须大于0 |
| `400103` | 每页条数不能超过100 |