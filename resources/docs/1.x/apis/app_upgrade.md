# 版本升级检查

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [返回参数说明](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

检查APP是否有新版本可供升级（对version_number进行比较），支持灰度发布和强制升级。

### 注意事项

1. 支持多渠道升级管理，默认渠道为"default"
2. 灰度发布逻辑：
   - 通过device_uuid使用crc32算法计算得到一个值，所以`请务必保证同一设备uuid在app安装后一直不变`。
   - 该值与灰度百分比进行比较，决定该设备是否在灰度范围内
   - 只有在灰度范围内的设备才会收到升级提示
3. 强制升级逻辑：
   - 当服务端配置为强制升级时，客户端必须升级才能继续使用
   - 当客户端版本号低于配置的最小版本号时，也会触发强制升级
4. 版本号使用整数形式，便于比较大小

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| GET | `/v1/app/upgrade` | [签名认证](/{{route}}/{{version}}/intro#section-3) |

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
| device_uuid | string | 是 | 最大128字符 | - | 设备唯一标识，用于灰度发布判断 |
| platform | integer | 是 | 1,2,3,99 | - | 平台类型（1:iOS, 2:Android, 3:HarmonyOS, 99:其他） |
| version_number | integer | 是 | - | - | 当前版本值（整数） |
| channel_name | string | 否 | 最大32字符 | default | 渠道名称 |

<a name="section-4"></a>
## 请求示例代码

```javascript
curl --location --request GET 'https://api.zaihangyun.com/v1/app/upgrade?appkey=D5fceA1sVtmaMY1F&timestamp=1650381362&sign=5HC0kxCm2jsR3DpzhRJEFY3IfFvTcCN-8-qvvLCrKB8&device_uuid=abc123456789&platform=2&version_number=100&channel_name=official' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive'
```

<a name="section-5"></a>
## 返回响应

- 成功返回（有新版本，需要强制升级）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "upgrade": true,
        "force_upgrade": true,
        "last_version": "1.2.0",
        "last_version_number": 12,
        "upgrade_from": 1,
        "platform": 1,
        "package_download_url": "https://download.example.com/app/v1.2.0.apk",
        "package_size": "10.1M",
        "package_md5": "a1b2c3d4e5f6g7h8i9j0",
        "upgrade_note": "1. 修复已知问题\n2. 优化用户体验\n3. 新增功能"
    }
}
```

- 成功返回（有新版本，非强制升级）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "upgrade": true,
        "force_upgrade": false,
        "last_version": "1.2.0",
        "last_version_number": 12,
        "upgrade_from": 1,
        "platform": 1,
        "package_download_url": "https://download.example.com/app/v1.2.0.apk",
        "package_size": "10.1M",
        "package_md5": "a1b2c3d4e5f6g7h8i9j0",
        "upgrade_note": "1. 修复已知问题\n2. 优化用户体验\n3. 新增功能"
    }
}
```

- 成功返回（无需升级或不在灰度范围内）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "upgrade": false,
        "force_upgrade": false,
        "last_version": "1.2.0",
        "last_version_number": 12,
        "upgrade_from": 1,
        "platform": 1,
        "package_download_url": "https://download.example.com/app/v1.2.0.apk",
        "package_size": "10.1M",
        "package_md5": "a1b2c3d4e5f6g7h8i9j0",
        "upgrade_note": "1. 修复已知问题\n2. 优化用户体验\n3. 新增功能"
    }
}
```

- 错误返回

```json
{
    "code": 400198,
    "msg": "upgrade channel not found"
}
```

<a name="section-6"></a>
## 返回参数说明

| 参数名 | 类型 | 说明 |
| -- | -- | -- |
| upgrade | boolean | 是否需要升级 |
| force_upgrade | boolean | 是否强制升级 |
| last_version | string | 最新版本号字符串 |
| last_version_number | integer | 最新版本号数值 |
| upgrade_from | integer | 升级方式：1:应用市场升级，2:官方链接下载或者其它方式 |
| platform | string | 平台类型：1:iOS, 2:Android, 3:HarmonyOS, 99:其他 |
| package_download_url | string | 安装包下载地址 |
| package_size | string | 安装包大小 |
| package_md5 | string | 安装包MD5校验值 |
| upgrade_note | string | 升级说明，注意：包含换行符 |

<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | device_uuid参数缺失 |
| `400102` | device_uuid必须为字符串类型 |
| `400103` | device_uuid长度不能超过128个字符 |
| `400104` | channel_name必须为字符串类型 |
| `400105` | channel_name长度不能超过32个字符 |
| `400106` | platform参数缺失 |
| `400107` | platform必须为整数类型 |
| `400108` | platform值必须在1,2,3,99范围内 |
| `400109` | version_number参数缺失 |
| `400110` | version_number必须为整数类型 |
| `400198` | 升级渠道不存在 |
| `400199` | 版本记录不存在 |