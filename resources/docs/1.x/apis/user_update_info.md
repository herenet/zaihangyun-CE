# 更新用户信息

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [错误码说明](#section-6)

<a name="section-1"></a>
## 接口说明

更新当前登录用户的个人信息

该接口需要在APP后台启用登录

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/user/update` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| nickname | string | 最大32字符 | 否 | 用户昵称 |
| gender | integer | 0,1,2 | 否 | 性别：0-未知，1-男，2-女 |
| birthday | string | YYYY-MM-DD | 否 | 生日日期 |
| oaid | string | 最大128字符 | 否 | 设备唯一标识符 |
| device_id | string | 最大128字符 | 否 | 设备ID |
| password | string | 6-32字符 | 否 | 用户密码 |
| email | string | 有效邮箱格式 | 否 | 电子邮箱 |
| country | string | 最大32字符 | 否 | 国家 |
| province | string | 最大32字符 | 否 | 省份 |
| city | string | 最大32字符 | 否 | 城市 |
| enter_pass | string | 6-8字符 | 否 | 进入密码 |
| ext_data | JSON | 最大200字符的JSON | 否 | 扩展数据 |

> {warning} 不需要更新的字段禁传参数名；字段清空传值为空即可

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/user/update' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer MTc0NTY1Mzk1NQ==.fea11f8171c7576d1853321bc3164e40.RDVmY2VBMXNWdG1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: multipart/form-data; boundary=--------------------------016597420824478164980692' \
--form 'nickname="herenet"' \
--form 'gender="1"' \
--form 'birthday="1997-03-04"' \
--form 'oaid="4123123132"' \
--form 'password="123456"' \
--form 'email="zaihangyun@126.com"' \
--form 'country=""' \
--form 'province=""' \
--form 'city=""' \
--form 'enter_pass="111111"' \
--form 'ext_data="{    \"device_id\": \"7290832492f7bccf\",    \"c_number\": \"huawei\",    \"packagename\": \"com.ape.apefather\",    \"d_id\": \"16181\"}"'
```

- application/json方式：

```javascript
curl --location --request POST 'http://127.0.0.1:8787/v1/user/update' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer MTc0NTIxOTg4OQ==.1c7f037215a4bde32073b5abf1c8a6c4.RDVmY2VBMXNWdG1hTVkxRi4yLjE4NTcyNDgzMjQ=' \
--header 'Accept: */*' \
--header 'Host: 127.0.0.1:8787' \
--header 'Connection: keep-alive' \
--data-raw '{
  "nickname": "herenet1",
  "gender": "1",
  "birthday": "1997-03-04",
  "oaid": "4123123132",
  "password": "123456",
  "email": "zaihangyun@126.com",
  "country": "",
  "province": "",
  "city": "",
  "enter_pass": "111111",
  "ext_data": {
    "device_id": "7290832492f7bccf",
    "c_number": "huawei",
    "packagename": "com.ape.apefather",
    "d_id": "16181"
  }
}'
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
    "code": "400102",
    "msg": "nickname length must be less than 32 characters"
}
```


<a name="section-6"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | nickname必须是字符串 |
| `400102` | nickname长度不能超过32个字符 |
| `400103` | gender必须是整数 |
| `400105` | gender必须是0,1,2中的一个 |
| `400106` | birthday必须是有效的日期格式 |
| `400107` | oaid必须是字符串 |
| `400108` | oaid长度不能超过128个字符 |
| `400109` | device_id必须是字符串 |
| `400110` | device_id长度不能超过128个字符 |
| `400113` | password必须是字符串 |
| `400114` | password长度必须大于6个字符 |
| `400115` | password长度不能超过32个字符 |
| `400116` | email必须是有效的邮箱地址 |
| `400117` | country必须是字符串 |
| `400118` | country长度不能超过32个字符 |
| `400119` | province必须是字符串 |
| `400120` | province长度不能超过32个字符 |
| `400121` | city必须是字符串 |
| `400122` | city长度不能超过32个字符 |
| `400123` | enter_pass必须是字符串 |
| `400124` | enter_pass长度必须大于4个字符 |
| `400125` | enter_pass长度不能超过8个字符 |
| `400126` | ext_data必须是有效的JSON |
| `400127` | ext_data长度不能超过200个字符 |
| `400199` | 用户不存在 |
| `400250` | 更新用户信息失败 |