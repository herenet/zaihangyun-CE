# 接入需知

---
- [API规范](#section-1)
- [安全认证](#section-2)

<a name="section-1"></a>
## API规范
### 接口请求
在行云采用统一的接口风格。所有获取数据类的接口统一采用HTTP GET方式请求，所有修改数据类的接口采用HTTP POST方式请求。

### 请求参数
- GET类请求参数需通过URL Query String方式传递（格式为`?key1=value1&key2=value2`）。

- POST类请求：
  1. 内容类型：支持以下格式
     - `application/x-www-form-urlencoded`：标准表单编码数据
     - `multipart/form-data`：用于包含文件上传的表单数据
     - `application/json`：JSON格式请求体
  2. 字符编码：统一采用UTF-8编码
  3. 文件上传：必须使用`multipart/form-data`格式，通过标准的文件字段提交

### 返回数据结构
返回数据格式为JSON，数据结构如下：


| 字段名 | 是否必须 | 数据类型 | 字段说明 | 
| -- | -- | -- | -- |
| code | 是 | unsigned int | 状态码：详见[错误码规则](/{{route}}/{{version}}/code) |
| msg | 是 | string | 响应消息 |
| data | 否 | object/array | 当有数据返回时包含该字段，具体格式参见[接口列表](/{{route}}/{{version}}/function/apis) |

示例:

- 返回单条数据：

```json
{
    "code": 200, 
    "msg": "success", 
    "data": {
        "uid": 1, 
        "name": "name1"
    }
}
```

- 返回列表数据：

```json
{
    "code": 200, 
    "msg": "success", 
    "data": [
        {"uid": 1, "name": "name1"}, 
        {"uid": 2, "name": "name2"}
    ]
}
```

- 返回空数据：

```json
{
    "code": 200, 
    "msg": "success", 
    "data": []
}
```

- 返回错误信息：

```json
{
    "code": 400102,
    "msg": "status must be in 1,2"
}
```



<a name="section-2"></a>
## 安全认证

接口安全认证分为两类：
### 签名认证

用于非登录态接口，例如：用户注册、登录等。为简化接入复杂度，签名无需对所有参数进行排序拼接，仅需按照如下固定顺序，对appkey、timestamp、appSecret三个值直接拼接后进行MD5加密即可。

- 参数说明：

| 字段名 | 字段说明 |
| -- | -- |
| appkey | 在行云平台为您创建的应用提供的唯一标识 |
| timestamp | 当前时间戳（秒），服务端允许±120秒的误差范围 |
| appSecret | 在行云平台为您创建的应用提供的密钥，请妥善保管 |

> {warning} 请严格按照参数拼接顺序：appkey+timestamp+appSecret，不包含任何连接符

- Android签名示例代码：

```java
package com.example.signatureexample;

import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

public class MainActivity extends AppCompatActivity {

    private static final String TAG = "SignatureExample";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        TextView resultTextView = findViewById(R.id.resultTextView);
        
        // 示例参数
        String appKey = "your_app_key";
        // 使用秒数作为timestamp
        String timestamp = String.valueOf(System.currentTimeMillis() / 1000);
        String appSecret = "your_app_secret";
        
        // 生成签名
        String signature = generateMD5Signature(appKey, timestamp, appSecret);
        
        // 显示结果
        String result = "原始字符串: " + appKey + timestamp + appSecret + "\n\n";
        result += "MD5签名: " + signature;
        
        resultTextView.setText(result);
        Log.d(TAG, result);
    }
    
    /**
     * 生成MD5签名
     * @param appKey 应用的AppKey
     * @param timestamp 时间戳（秒数）
     * @param appSecret 应用的AppSecret
     * @return MD5签名字符串
     */
    public String generateMD5Signature(String appKey, String timestamp, String appSecret) {
        // 按顺序连接参数
        String stringToSign = appKey + timestamp + appSecret;
        
        try {
            // 创建MD5摘要算法实例
            MessageDigest md = MessageDigest.getInstance("MD5");
            
            // 计算MD5摘要
            byte[] digest = md.digest(stringToSign.getBytes());
            
            // 将字节数组转换为十六进制字符串
            StringBuilder sb = new StringBuilder();
            for (byte b : digest) {
                sb.append(String.format("%02x", b & 0xff));
            }
            
            return sb.toString();
            
        } catch (NoSuchAlgorithmException e) {
            e.printStackTrace();
            return null;
        }
    }
}
```

- ApiFox前置操作脚本示例：

```javascript
const Moment = require('moment');
const timestamp = Moment().unix();
pm.environment.set('timestamp', timestamp);

// 收集需要参与签名的参数
let param = {};

// 处理 Query 参数
pm.request.url.query.each(item => {
  if (!item.disabled && item.value !== '') {
    param[item.key] = item.value;
  }
});

// 处理 Body 参数
if (pm.request.body) {
  let bodyData;
  switch (pm.request.body.mode) {
    case 'formdata':
      bodyData = pm.request.body.formdata;
      break;
    case 'urlencoded':
      bodyData = pm.request.body.urlencoded;
      break;
    case 'raw': {
      const contentType = pm.request.headers.get('content-type');
      if (contentType && contentType.toLowerCase().includes('application/json')) {
        try {
          const jsonData = JSON.parse(pm.request.body.raw);
          for (let key in jsonData) {
            if (jsonData[key] !== '') {
              param[key] = jsonData[key];
            }
          }
        } catch (e) {
          console.log('请求 body 不是 JSON 格式');
        }
      }
      break;
    }
    default:
      break;
  }
  if (bodyData) {
    bodyData.each(item => {
      if (!item.disabled && item.value !== '') {
        param[item.key] = item.value;
      }
    });
  }
}
const secretKey = pm.environment.get('APP_SECRET');

// 生成签名
const paramString = param['appkey']+timestamp+secretKey;
const sign = CryptoJS.MD5(paramString).toString();
pm.environment.set('sign', sign);
```

### Token认证

用于需要用户登录后才能访问的接口，例如：下单、购买、修改用户信息等。在行云平台为支持多端登录管理，采用了有状态的Token机制。用户登录成功后会获得一个Token，在后续请求中，需要在HTTP请求头中通过Bearer认证方式携带此Token。

- HTTP请求头示例：

```http
GET /api/user/info HTTP/1.1
Host: api.zaihangyun.com
User-Agent: YourApp/1.0.0
Authorization: Bearer MTc0NTIxOTg4OQ==.1c7f037XXXXXXXXxxxxxxxxf1c8a6c4.RDVmY2VBMXNWdG1hTVkxRi4yLjE4NTcyNDgzMjQ=
Accept: */*
Connection: keep-alive
```


- Android添加Token认证示例代码：

```java
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import okhttp3.Call;
import okhttp3.Callback;
import java.io.IOException;

public class ApiClient {

    private static final String BASE_URL = "https://api.zaihangyun.com";
    private static final String TOKEN = "your_access_token"; // 实际应用中应从安全存储中获取

    public void makeAuthenticatedRequest() {
        OkHttpClient client = new OkHttpClient();

        // 创建请求并添加Bearer Token到Authorization头
        Request request = new Request.Builder()
                .url(BASE_URL + "/user/info")
                .header("Authorization", "Bearer " + TOKEN)
                .build();

        // 执行请求
        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(Call call, IOException e) {
                e.printStackTrace();
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException {
                if (response.isSuccessful()) {
                    String responseData = response.body().string();
                    // 处理响应数据
                }
            }
        });
    }
}
```