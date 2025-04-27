#获取文档内容（Markdown源码）

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

获取指定ID的文章详细信息（Markdown源码）。如需已渲染的文章内容，可直接使用文档列表中返回的Link字段。

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/article/info` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| appkey | string | - | 是 | 在行云为应用分配的appkey |
| timestamp | string | 10位数字 | 是 | 当前时间戳（秒级） |
| sign | string | 32位小写字母和数字 | 是 | 签名，算法为：md5(appkey + timestamp + app_secret)，app_secret为在行云为应用分配的appSecret |

### 业务参数
| 参数名 | 类型 | 是否必须 | 说明 |
| -- | -- | -- | -- |
| id | string | 是 | 文章ID |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'http:s//api.zaihangyun.com/v1/article/info?id=12515714234&appkey=D5fceA1sxxmaMY1F&timestamp=1745584146&sign=15f92fb3a893efcadf0dd745ff591c77' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（[查看字段解析](/{{route}}/{{version}}/struct#section-3)）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "id": "12515714234",
        "title": "中华人民共和国",
        "content": "# 中华人民共和国\r\n## ssssssssssssssss\r\n![](https://www.zaihangyun.com/storage/mch/2/article/e0ab13bce3aecb796df0d157de5e0fb4.png)\r\n\r\n1. 中华人民共和国中华人民共和国\r\n2. dsafsdf\r\n3. sdaf\r\n4. sdadf\r\n5. dsafdsdsa\r\n6. sdad\r\n7. 在sf\r\n8. 顶戴\r\n\r\n```html\r\nsdfadsafs\r\n```\r\n\r\n\r\n------------\r\n\r\n[dsafdsasdfafdsaf](http://www.baidu.com \"dsafdsasdfafdsaf\")\r\n\r\n> dsafdsafdsafdsaf\r\n> dsafsdfdsafdsfsdafds",
        "order": 1,
        "updated_at": "2025-04-19 19:44:05",
        "created_at": "2025-04-19 19:33:26"
    }
}
```

- 错误返回

```json
{
    "code": 404,
    "msg": "article not found"
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | 参数错误：id参数缺失 |